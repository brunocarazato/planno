<?php

namespace Tests\Feature\Autenticacao;

use App\Models\User;
use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GerenciarSessaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_redireciona_login_para_inicio_com_modal_aberta(): void
    {
        $this->get('/entrar')
            ->assertRedirect('/?login=1');
    }

    public function test_professor_logado_que_acessa_login_vai_para_dashboard(): void
    {
        $usuario = User::factory()->create([
            'ra' => 'PROF001',
            'tipo' => User::TIPO_PROFESSOR,
        ]);

        $this->actingAs($usuario)
            ->get('/entrar')
            ->assertRedirect('/dashboard');
    }

    public function test_aluno_logado_que_acessa_login_vai_para_projetos(): void
    {
        $usuario = User::factory()->create([
            'ra' => 'RA123',
            'tipo' => User::TIPO_ALUNO,
        ]);

        $this->actingAs($usuario)
            ->get('/entrar')
            ->assertRedirect('/projetos');
    }

    public function test_professor_entra_com_ra_e_senha(): void
    {
        $usuario = User::factory()->create([
            'ra' => 'PROF001',
            'tipo' => User::TIPO_PROFESSOR,
            'password' => 'senha-segura',
        ]);

        $this->post('/entrar', [
            'ra' => 'prof001',
            'password' => 'senha-segura',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($usuario);
    }

    public function test_aluno_entra_quando_possui_vinculo_aprovado_com_turma(): void
    {
        $usuario = User::factory()->create([
            'ra' => 'RA123',
            'tipo' => User::TIPO_ALUNO,
            'password' => 'senha-segura',
        ]);
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        CadastroAluno::create([
            'user_id' => $usuario->id,
            'turma_id' => $turma->id,
            'nome' => 'Ana Souza',
            'ra' => 'RA123',
            'status' => CadastroAluno::STATUS_APROVADO,
            'valido_ate' => now()->addYear()->toDateString(),
        ]);

        $this->post('/entrar', [
            'ra' => 'ra123',
            'password' => 'senha-segura',
        ])->assertRedirect('/projetos');

        $this->assertAuthenticatedAs($usuario);
    }

    public function test_aluno_nao_entra_sem_vinculo_aprovado_com_turma(): void
    {
        $usuario = User::factory()->create([
            'ra' => 'RA123',
            'tipo' => User::TIPO_ALUNO,
            'password' => 'senha-segura',
        ]);
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        CadastroAluno::create([
            'user_id' => $usuario->id,
            'turma_id' => $turma->id,
            'nome' => 'Ana Souza',
            'ra' => 'RA123',
            'status' => CadastroAluno::STATUS_PENDENTE,
        ]);

        $this->from('/?login=1')
            ->post('/entrar', [
                'ra' => 'ra123',
                'password' => 'senha-segura',
            ])
            ->assertRedirect('/?login=1')
            ->assertSessionHasErrors('ra');

        $this->assertGuest();
    }

    public function test_aluno_nao_entra_com_vinculo_vencido(): void
    {
        $usuario = User::factory()->create([
            'ra' => 'RA123',
            'tipo' => User::TIPO_ALUNO,
            'password' => 'senha-segura',
        ]);
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        CadastroAluno::create([
            'user_id' => $usuario->id,
            'turma_id' => $turma->id,
            'nome' => 'Ana Souza',
            'ra' => 'RA123',
            'status' => CadastroAluno::STATUS_APROVADO,
            'valido_ate' => now()->subDay()->toDateString(),
        ]);

        $this->from('/?login=1')
            ->post('/entrar', [
                'ra' => 'ra123',
                'password' => 'senha-segura',
            ])
            ->assertRedirect('/?login=1')
            ->assertSessionHasErrors('ra');

        $this->assertGuest();
    }

    public function test_recusa_credenciais_invalidas(): void
    {
        User::factory()->create([
            'ra' => 'RA123',
            'password' => 'senha-segura',
        ]);

        $this->from('/?login=1')
            ->post('/entrar', [
                'ra' => 'RA123',
                'password' => 'senha-errada',
            ])
            ->assertRedirect('/?login=1')
            ->assertSessionHasErrors('ra');

        $this->assertGuest();
    }

    public function test_usuario_sai_da_aplicacao(): void
    {
        $usuario = User::factory()->create([
            'ra' => 'RA123',
            'password' => 'senha-segura',
        ]);

        $this->actingAs($usuario)
            ->post('/sair')
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_professor_pode_existir_via_banco(): void
    {
        $usuario = User::factory()->create([
            'ra' => 'PROF001',
            'tipo' => User::TIPO_PROFESSOR,
        ]);

        $this->assertTrue($usuario->professor());
        $this->assertFalse($usuario->aluno());
    }
}
