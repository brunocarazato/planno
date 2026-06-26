<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Projetos\Models\Projeto;
use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProfessorDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_nao_acessa_o_dashboard_do_professor(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/entrar');
    }

    public function test_aluno_nao_acessa_o_dashboard_do_professor(): void
    {
        $this->actingAs(User::factory()->create([
            'tipo' => User::TIPO_ALUNO,
        ]));

        $this->get('/dashboard')
            ->assertForbidden();
    }

    public function test_professor_visualiza_metricas_de_projetos_turmas_e_alunos(): void
    {
        $this->actingAs(User::factory()->create([
            'tipo' => User::TIPO_PROFESSOR,
        ]));

        $turmaAtiva = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-2026-1A',
            'aceita_novos_cadastros' => true,
        ]);

        Turma::create([
            'nome' => 'Turma bloqueada',
            'codigo' => 'GP-2026-1B',
            'aceita_novos_cadastros' => false,
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
        ]);

        Projeto::create([
            'turma_id' => $turmaAtiva->id,
            'nome' => 'Projeto sem situacao mapeada',
            'codigo' => 'PROJ-002',
            'situacao' => 'planejamento',
        ]);

        CadastroAluno::create([
            'turma_id' => $turmaAtiva->id,
            'nome' => 'Ana Souza',
            'ra' => 'RA123',
            'status' => CadastroAluno::STATUS_PENDENTE,
        ]);

        CadastroAluno::create([
            'turma_id' => $turmaAtiva->id,
            'nome' => 'Bruno Lima',
            'ra' => 'RA456',
            'status' => CadastroAluno::STATUS_APROVADO,
            'valido_ate' => now()->addMonth()->toDateString(),
        ]);

        CadastroAluno::create([
            'turma_id' => $turmaAtiva->id,
            'nome' => 'Carla Dias',
            'ra' => 'RA789',
            'status' => CadastroAluno::STATUS_APROVADO,
            'valido_ate' => now()->subDay()->toDateString(),
        ]);

        $this->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Professor')
                ->where('metricas.projetos.total', 2)
                ->where('metricas.projetos.emIniciacao', 1)
                ->where('metricas.turmas.total', 3)
                ->where('metricas.turmas.ativas', 2)
                ->where('metricas.turmas.aceitandoCadastros', 1)
                ->where('metricas.alunos.cadastros', 3)
                ->where('metricas.alunos.pendentes', 1)
                ->where('metricas.alunos.aprovadosAtivos', 1));
    }
}
