<?php

namespace Tests\Feature\Turmas;

use App\Models\User;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GerenciarTurmasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->actingAs(User::factory()->create([
            'tipo' => User::TIPO_PROFESSOR,
        ]));
    }

    public function test_visitante_nao_acessa_a_rota_de_turmas(): void
    {
        auth()->logout();

        $this->get('/turmas')
            ->assertRedirect('/entrar');
    }

    public function test_aluno_nao_acessa_a_rota_de_turmas(): void
    {
        auth()->logout();

        $this->actingAs(User::factory()->create([
            'tipo' => User::TIPO_ALUNO,
        ]));

        $this->get('/turmas')
            ->assertForbidden();
    }

    public function test_exibe_a_tela_de_turmas(): void
    {
        Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'periodo' => '1',
            'ano' => 2026,
            'aceita_novos_cadastros' => true,
        ]);

        $this->get('/turmas')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Turmas/Index')
                ->has('turmas', 1)
                ->where('turmas.0.periodoFormatado', '1º Semestre de 2026')
                ->where('metricas.total', 1)
                ->where('metricas.ativas', 1)
                ->where('metricas.aceitandoCadastros', 1));
    }

    public function test_cria_uma_turma_ativa_aceitando_cadastros(): void
    {
        $this->post('/turmas', [
            'nome' => 'Gestao de Projetos',
            'periodo' => '1',
            'ano' => 2026,
            'descricao' => 'Turma piloto do MVP.',
        ])->assertRedirect('/turmas');

        $this->assertDatabaseHas('turmas', [
            'nome' => 'Gestao de Projetos',
            'codigo' => 'TUR-2026-1-001',
            'periodo' => '1',
            'ano' => 2026,
            'descricao' => 'Turma piloto do MVP.',
            'aceita_novos_cadastros' => true,
            'arquivada_em' => null,
        ]);
    }

    public function test_gera_codigo_sequencial_ao_criar_turma(): void
    {
        Turma::create([
            'nome' => 'Turma existente',
            'codigo' => 'TUR-2026-1-001',
            'aceita_novos_cadastros' => true,
        ]);

        $this->post('/turmas', [
            'nome' => 'Nova turma',
            'periodo' => '1',
            'ano' => 2026,
        ])->assertRedirect('/turmas');

        $this->assertDatabaseHas('turmas', [
            'nome' => 'Nova turma',
            'codigo' => 'TUR-2026-1-002',
        ]);
    }

    public function test_atualiza_os_dados_basicos_da_turma(): void
    {
        $turma = Turma::create([
            'nome' => 'Nome antigo',
            'codigo' => 'ANTIGA',
            'aceita_novos_cadastros' => true,
        ]);

        $this->put("/turmas/{$turma->id}", [
            'nome' => 'Nome atualizado',
            'codigo' => 'nova',
            'periodo' => '2',
            'ano' => 2026,
            'descricao' => 'Descricao atualizada.',
        ])->assertRedirect('/turmas');

        $this->assertDatabaseHas('turmas', [
            'id' => $turma->id,
            'nome' => 'Nome atualizado',
            'codigo' => 'ANTIGA',
            'periodo' => '2',
            'ano' => 2026,
            'descricao' => 'Descricao atualizada.',
        ]);
    }

    public function test_valida_periodo_e_ano_da_turma(): void
    {
        $this->from('/turmas')
            ->post('/turmas', [
                'nome' => 'Gestao de Projetos',
                'periodo' => '3',
                'ano' => '26',
            ])
            ->assertRedirect('/turmas')
            ->assertSessionHasErrors(['periodo', 'ano']);
    }

    public function test_permite_bloqueia_e_arquiva_turma(): void
    {
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        $this->patch("/turmas/{$turma->id}/bloquear-cadastros")
            ->assertRedirect('/turmas');

        $this->assertDatabaseHas('turmas', [
            'id' => $turma->id,
            'aceita_novos_cadastros' => false,
        ]);

        $this->patch("/turmas/{$turma->id}/permitir-cadastros")
            ->assertRedirect('/turmas');

        $this->assertDatabaseHas('turmas', [
            'id' => $turma->id,
            'aceita_novos_cadastros' => true,
        ]);

        $this->patch("/turmas/{$turma->id}/arquivar")
            ->assertRedirect('/turmas');

        $turma->refresh();

        $this->assertFalse($turma->aceita_novos_cadastros);
        $this->assertNotNull($turma->arquivada_em);
    }

    public function test_nao_reabre_cadastros_de_turma_arquivada(): void
    {
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => false,
            'arquivada_em' => now(),
        ]);

        $this->patch("/turmas/{$turma->id}/permitir-cadastros")
            ->assertRedirect('/turmas');

        $this->assertFalse($turma->refresh()->aceita_novos_cadastros);
    }
}
