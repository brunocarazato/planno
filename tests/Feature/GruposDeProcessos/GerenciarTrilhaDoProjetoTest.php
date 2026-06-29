<?php

namespace Tests\Feature\GruposDeProcessos;

use App\Models\User;
use App\Modules\Projetos\Models\Projeto;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GerenciarTrilhaDoProjetoTest extends TestCase
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

    public function test_exibe_grupos_atividades_artefatos_e_progresso_da_trilha(): void
    {
        $projeto = $this->criarProjeto();

        $this->get("/projetos/{$projeto->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projetos/Show')
                ->has('trilha.grupos', 5)
                ->where('trilha.grupos.0.nome', 'Iniciação')
                ->where('trilha.grupos.0.atividades.0.titulo', 'Elaborar o termo de abertura')
                ->where('trilha.grupos.0.atividades.0.artefato', 'Termo de abertura')
                ->where('trilha.grupos.4.nome', 'Encerramento')
                ->where('trilha.progresso.total', 12)
                ->where('trilha.progresso.concluidas', 0)
                ->where('trilha.progresso.percentual', 0));

        $this->assertDatabaseHas('trilhas_grupos_processos', [
            'projeto_id' => $projeto->id,
        ]);
    }

    public function test_marca_atividade_como_concluida_e_atualiza_progresso(): void
    {
        $projeto = $this->criarProjeto();
        $this->get("/projetos/{$projeto->id}")->assertOk();

        $this->patch("/projetos/{$projeto->id}/trilha/atividades/elaborar-termo-de-abertura", [
            'concluida' => true,
        ])->assertRedirect("/projetos/{$projeto->id}");

        $this->assertDatabaseHas('conclusoes_atividades_grupos_processos', [
            'chave_atividade' => 'elaborar-termo-de-abertura',
            'concluida_por' => auth()->id(),
        ]);

        $this->get("/projetos/{$projeto->id}")
            ->assertInertia(fn (Assert $page) => $page
                ->where('trilha.grupos.0.atividades.0.concluida', true)
                ->where('trilha.grupos.0.atividades.0.concluidaPor', auth()->user()?->name)
                ->where('trilha.grupos.0.progresso.concluidas', 1)
                ->where('trilha.grupos.0.progresso.percentual', 50)
                ->where('trilha.progresso.concluidas', 1)
                ->where('trilha.progresso.percentual', 8));
    }

    public function test_reabre_atividade_concluida(): void
    {
        $projeto = $this->criarProjeto();
        $this->get("/projetos/{$projeto->id}")->assertOk();

        $url = "/projetos/{$projeto->id}/trilha/atividades/elaborar-termo-de-abertura";

        $this->patch($url, ['concluida' => true])->assertRedirect();
        $this->patch($url, ['concluida' => false])->assertRedirect();

        $this->assertDatabaseMissing('conclusoes_atividades_grupos_processos', [
            'chave_atividade' => 'elaborar-termo-de-abertura',
        ]);
    }

    public function test_recusa_atividade_que_nao_pertence_ao_catalogo(): void
    {
        $projeto = $this->criarProjeto();
        $this->get("/projetos/{$projeto->id}")->assertOk();

        $this->from("/projetos/{$projeto->id}")
            ->patch("/projetos/{$projeto->id}/trilha/atividades/atividade-inexistente", [
                'concluida' => true,
            ])
            ->assertRedirect("/projetos/{$projeto->id}")
            ->assertSessionHasErrors('atividade');

        $this->assertDatabaseCount('conclusoes_atividades_grupos_processos', 0);
    }

    public function test_aluno_nao_altera_trilha_de_projeto_de_outro_responsavel(): void
    {
        $aluno = User::factory()->create(['tipo' => User::TIPO_ALUNO]);
        $outroAluno = User::factory()->create(['tipo' => User::TIPO_ALUNO]);
        $projeto = $this->criarProjeto($outroAluno);

        $this->actingAs($aluno)
            ->patch("/projetos/{$projeto->id}/trilha/atividades/elaborar-termo-de-abertura", [
                'concluida' => true,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('conclusoes_atividades_grupos_processos', 0);
    }

    private function criarProjeto(?User $responsavel = null): Projeto
    {
        $turma = Turma::create([
            'nome' => 'Gestao de Projetos',
            'codigo' => 'GP-'.fake()->unique()->numerify('####'),
            'aceita_novos_cadastros' => true,
        ]);

        $projeto = Projeto::create([
            'turma_id' => $turma->id,
            'responsavel_id' => $responsavel?->id ?? auth()->id(),
            'nome' => 'Projeto didático',
            'codigo' => 'PROJ-'.fake()->unique()->numerify('####'),
            'situacao' => Projeto::SITUACAO_EM_INICIACAO,
        ]);

        $projeto->termoDeAbertura()->create();

        return $projeto;
    }
}
