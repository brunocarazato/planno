<?php

namespace Tests\Feature\Projetos;

use App\Modules\Projetos\Models\Projeto;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GerenciarProjetosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_exibe_a_tela_de_projetos_com_turmas_ativas(): void
    {
        $turmaAtiva = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'periodo' => '2026.1',
            'aceita_novos_cadastros' => true,
        ]);

        Turma::create([
            'nome' => 'Turma arquivada',
            'codigo' => 'GP-2025-2A',
            'aceita_novos_cadastros' => false,
            'arquivada_em' => now(),
        ]);

        Projeto::create([
            'turma_id' => $turmaAtiva->id,
            'nome' => 'Projeto piloto',
            'codigo' => 'PROJ-001',
            'situacao' => Projeto::SITUACAO_EM_INICIACAO,
        ])->termoDeAbertura()->create();

        $this->get('/projetos')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projetos/Index')
                ->has('projetos', 1)
                ->has('turmas', 1)
                ->where('turmas.0.codigo', 'GP-2026-1A')
                ->where('metricas.total', 1)
                ->where('metricas.emIniciacao', 1));
    }

    public function test_cria_projeto_vinculado_a_turma_ativa_com_termo_de_abertura(): void
    {
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        $this->post('/projetos', [
            'turma_id' => $turma->id,
            'nome' => 'Sistema de biblioteca escolar',
            'codigo' => 'proj-2026-01',
            'descricao' => 'Projeto didatico do primeiro semestre.',
        ])->assertRedirect();

        $projeto = Projeto::query()->firstOrFail();

        $this->assertDatabaseHas('projetos', [
            'id' => $projeto->id,
            'turma_id' => $turma->id,
            'nome' => 'Sistema de biblioteca escolar',
            'codigo' => 'PROJ-2026-01',
            'situacao' => Projeto::SITUACAO_EM_INICIACAO,
        ]);

        $this->assertDatabaseHas('termos_de_abertura', [
            'projeto_id' => $projeto->id,
        ]);
    }

    public function test_nao_cria_projeto_para_turma_arquivada(): void
    {
        $turma = Turma::create([
            'nome' => 'Turma arquivada',
            'codigo' => 'GP-2025-2A',
            'aceita_novos_cadastros' => false,
            'arquivada_em' => now(),
        ]);

        $this->from('/projetos')
            ->post('/projetos', [
                'turma_id' => $turma->id,
                'nome' => 'Projeto indevido',
                'codigo' => 'PROJ-ARQ',
            ])
            ->assertRedirect('/projetos')
            ->assertSessionHasErrors('turma_id');

        $this->assertDatabaseCount('projetos', 0);
    }

    public function test_exibe_o_detalhe_do_projeto(): void
    {
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'periodo' => '2026.1',
            'aceita_novos_cadastros' => true,
        ]);

        $projeto = Projeto::create([
            'turma_id' => $turma->id,
            'nome' => 'Sistema de biblioteca escolar',
            'codigo' => 'PROJ-2026-01',
            'situacao' => Projeto::SITUACAO_EM_INICIACAO,
        ]);

        $projeto->termoDeAbertura()->create([
            'objetivo' => 'Organizar emprestimos de livros.',
        ]);

        $this->get("/projetos/{$projeto->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projetos/Show')
                ->where('projeto.nome', 'Sistema de biblioteca escolar')
                ->where('projeto.turma.codigo', 'GP-2026-1A')
                ->where('projeto.termoDeAbertura.objetivo', 'Organizar emprestimos de livros.'));
    }

    public function test_atualiza_o_termo_de_abertura_do_projeto(): void
    {
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        $projeto = Projeto::create([
            'turma_id' => $turma->id,
            'nome' => 'Sistema de biblioteca escolar',
            'codigo' => 'PROJ-2026-01',
            'situacao' => Projeto::SITUACAO_EM_INICIACAO,
        ]);

        $projeto->termoDeAbertura()->create();

        $this->put("/projetos/{$projeto->id}/termo-de-abertura", [
            'objetivo' => 'Organizar emprestimos de livros.',
            'justificativa' => 'Reduzir controles manuais.',
            'restricoes' => 'Prazo de oito semanas.',
            'premissas' => 'A turma tera acesso ao laboratorio.',
            'entregas_esperadas' => 'Prototipo navegavel e relatorio final.',
        ])->assertRedirect("/projetos/{$projeto->id}");

        $this->assertDatabaseHas('termos_de_abertura', [
            'projeto_id' => $projeto->id,
            'objetivo' => 'Organizar emprestimos de livros.',
            'justificativa' => 'Reduzir controles manuais.',
            'restricoes' => 'Prazo de oito semanas.',
            'premissas' => 'A turma tera acesso ao laboratorio.',
            'entregas_esperadas' => 'Prototipo navegavel e relatorio final.',
        ]);
    }
}
