<?php

namespace Tests\Feature\Turmas;

use App\Models\User;
use App\Modules\Turmas\Actions\ExpirarCadastrosDeAlunoVencidos;
use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GerenciarCadastrosAlunosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_exibe_a_tela_publica_com_turmas_ativas_aceitando_cadastros(): void
    {
        Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'periodo' => '1',
            'ano' => 2026,
            'aceita_novos_cadastros' => true,
        ]);

        Turma::create([
            'nome' => 'Turma bloqueada',
            'codigo' => 'GP-2026-1B',
            'aceita_novos_cadastros' => false,
        ]);

        $this->get('/cadastros-alunos/solicitar')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('CadastrosAlunos/Solicitar')
                ->has('turmas', 1)
                ->where('turmas.0.codigo', 'GP-2026-1A'));
    }

    public function test_aluno_logado_nao_visualiza_tela_de_solicitacao_de_cadastro(): void
    {
        $usuario = User::factory()->create([
            'tipo' => User::TIPO_ALUNO,
        ]);

        $this->actingAs($usuario)
            ->get('/cadastros-alunos/solicitar')
            ->assertRedirect('/projetos');
    }

    public function test_aluno_solicita_cadastro_para_turma_ativa(): void
    {
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        $this->post('/cadastros-alunos', [
            'turma_id' => $turma->id,
            'nome' => 'Ana Souza',
            'ra' => 'ra123',
            'password' => 'senha-segura',
            'password_confirmation' => 'senha-segura',
        ])->assertRedirect('/cadastros-alunos/solicitar');

        $usuario = User::query()->where('ra', 'RA123')->firstOrFail();

        $this->assertSame(User::TIPO_ALUNO, $usuario->tipo);
        $this->assertSame('Ana Souza', $usuario->name);

        $this->assertDatabaseHas('cadastros_alunos', [
            'user_id' => $usuario->id,
            'turma_id' => $turma->id,
            'nome' => 'Ana Souza',
            'ra' => 'RA123',
            'status' => CadastroAluno::STATUS_PENDENTE,
        ]);
    }

    public function test_nao_solicita_cadastro_para_turma_bloqueada_ou_arquivada(): void
    {
        $turmaBloqueada = Turma::create([
            'nome' => 'Turma bloqueada',
            'codigo' => 'GP-2026-1B',
            'aceita_novos_cadastros' => false,
        ]);

        $this->from('/cadastros-alunos/solicitar')
            ->post('/cadastros-alunos', [
                'turma_id' => $turmaBloqueada->id,
                'nome' => 'Ana Souza',
                'ra' => 'RA123',
                'password' => 'senha-segura',
                'password_confirmation' => 'senha-segura',
            ])
            ->assertRedirect('/cadastros-alunos/solicitar')
            ->assertSessionHasErrors('turma_id');

        $turmaArquivada = Turma::create([
            'nome' => 'Turma arquivada',
            'codigo' => 'GP-2026-1C',
            'aceita_novos_cadastros' => true,
            'arquivada_em' => now(),
        ]);

        $this->from('/cadastros-alunos/solicitar')
            ->post('/cadastros-alunos', [
                'turma_id' => $turmaArquivada->id,
                'nome' => 'Bruno Lima',
                'ra' => 'RA456',
                'password' => 'senha-segura',
                'password_confirmation' => 'senha-segura',
            ])
            ->assertRedirect('/cadastros-alunos/solicitar')
            ->assertSessionHasErrors('turma_id');
    }

    public function test_nao_permite_ra_com_cadastro_pendente_ou_aprovado(): void
    {
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        CadastroAluno::create([
            'turma_id' => $turma->id,
            'nome' => 'Ana Souza',
            'ra' => 'RA123',
            'status' => CadastroAluno::STATUS_PENDENTE,
        ]);

        $this->from('/cadastros-alunos/solicitar')
            ->post('/cadastros-alunos', [
                'turma_id' => $turma->id,
                'nome' => 'Ana Souza',
                'ra' => 'ra123',
                'password' => 'senha-segura',
                'password_confirmation' => 'senha-segura',
            ])
            ->assertRedirect('/cadastros-alunos/solicitar')
            ->assertSessionHasErrors('ra');
    }

    public function test_exige_senha_confirmada_no_cadastro_de_aluno(): void
    {
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        $this->from('/cadastros-alunos/solicitar')
            ->post('/cadastros-alunos', [
                'turma_id' => $turma->id,
                'nome' => 'Ana Souza',
                'ra' => 'RA123',
                'password' => 'senha-segura',
                'password_confirmation' => 'senha-diferente',
            ])
            ->assertRedirect('/cadastros-alunos/solicitar')
            ->assertSessionHasErrors('password');

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('cadastros_alunos', 0);
    }

    public function test_nao_permite_ra_ja_associado_a_usuario(): void
    {
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        User::factory()->create([
            'ra' => 'RA123',
            'tipo' => User::TIPO_ALUNO,
        ]);

        $this->from('/cadastros-alunos/solicitar')
            ->post('/cadastros-alunos', [
                'turma_id' => $turma->id,
                'nome' => 'Ana Souza',
                'ra' => 'ra123',
                'password' => 'senha-segura',
                'password_confirmation' => 'senha-segura',
            ])
            ->assertRedirect('/cadastros-alunos/solicitar')
            ->assertSessionHasErrors('ra');

        $this->assertDatabaseCount('cadastros_alunos', 0);
    }

    public function test_lista_cadastros_pendentes_na_tela_de_turmas(): void
    {
        $this->actingAs(User::factory()->create([
            'tipo' => User::TIPO_PROFESSOR,
        ]));

        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        CadastroAluno::create([
            'turma_id' => $turma->id,
            'nome' => 'Ana Souza',
            'ra' => 'RA123',
            'status' => CadastroAluno::STATUS_PENDENTE,
        ]);

        $this->get('/turmas')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Turmas/Index')
                ->has('cadastrosPendentes', 1)
                ->where('metricas.cadastrosPendentes', 1)
                ->where('cadastrosPendentes.0.nome', 'Ana Souza'));
    }

    public function test_aprova_cadastro_com_validade_anual(): void
    {
        $this->actingAs(User::factory()->create([
            'tipo' => User::TIPO_PROFESSOR,
        ]));

        $this->travelTo(now()->setDate(2026, 6, 25)->startOfDay());

        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        $cadastro = CadastroAluno::create([
            'turma_id' => $turma->id,
            'nome' => 'Ana Souza',
            'ra' => 'RA123',
            'status' => CadastroAluno::STATUS_PENDENTE,
        ]);

        $this->patch("/cadastros-alunos/{$cadastro->id}/aprovar")
            ->assertRedirect('/turmas');

        $cadastro->refresh();

        $this->assertSame(CadastroAluno::STATUS_APROVADO, $cadastro->status);
        $this->assertSame('2027-06-25', $cadastro->valido_ate->toDateString());
        $this->assertTrue($cadastro->permiteParticipacaoAtiva());
    }

    public function test_reprova_cadastro_de_aluno(): void
    {
        $this->actingAs(User::factory()->create([
            'tipo' => User::TIPO_PROFESSOR,
        ]));

        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        $cadastro = CadastroAluno::create([
            'turma_id' => $turma->id,
            'nome' => 'Ana Souza',
            'ra' => 'RA123',
            'status' => CadastroAluno::STATUS_PENDENTE,
        ]);

        $this->patch("/cadastros-alunos/{$cadastro->id}/reprovar", [
            'motivo_reprovacao' => 'RA nao localizado.',
        ])->assertRedirect('/turmas');

        $this->assertDatabaseHas('cadastros_alunos', [
            'id' => $cadastro->id,
            'status' => CadastroAluno::STATUS_REPROVADO,
            'motivo_reprovacao' => 'RA nao localizado.',
        ]);
    }

    public function test_expira_cadastros_vencidos_e_bloqueia_participacao_ativa(): void
    {
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        $cadastro = CadastroAluno::create([
            'turma_id' => $turma->id,
            'nome' => 'Ana Souza',
            'ra' => 'RA123',
            'status' => CadastroAluno::STATUS_APROVADO,
            'valido_ate' => now()->subDay()->toDateString(),
        ]);

        $cadastroValidoAteHoje = CadastroAluno::create([
            'turma_id' => $turma->id,
            'nome' => 'Bruno Lima',
            'ra' => 'RA456',
            'status' => CadastroAluno::STATUS_APROVADO,
            'valido_ate' => now()->toDateString(),
        ]);

        $this->assertFalse($cadastro->permiteParticipacaoAtiva());
        $this->assertTrue($cadastroValidoAteHoje->permiteParticipacaoAtiva());

        $totalExpirado = app(ExpirarCadastrosDeAlunoVencidos::class)->executar();

        $this->assertSame(1, $totalExpirado);
        $this->assertSame(CadastroAluno::STATUS_EXPIRADO, $cadastro->refresh()->status);
        $this->assertSame(CadastroAluno::STATUS_APROVADO, $cadastroValidoAteHoje->refresh()->status);
    }
}
