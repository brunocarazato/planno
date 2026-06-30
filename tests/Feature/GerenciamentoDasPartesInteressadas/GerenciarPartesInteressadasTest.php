<?php

namespace Tests\Feature\GerenciamentoDasPartesInteressadas;

use App\Models\User;
use App\Modules\GerenciamentoDasPartesInteressadas\Models\ParteInteressada;
use App\Modules\Projetos\Models\Projeto;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GerenciarPartesInteressadasTest extends TestCase
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

    public function test_exibe_as_partes_interessadas_no_detalhe_do_projeto(): void
    {
        $projeto = $this->criarProjeto();

        $projeto->partesInteressadas()->create([
            'nome' => 'Diretora da escola',
            'papel' => 'Patrocinadora',
            'organizacao' => 'Escola Horizonte',
            'poder' => ParteInteressada::NIVEL_ALTO,
            'interesse' => ParteInteressada::NIVEL_MEDIO,
            'estrategia_engajamento' => 'Realizar uma reunião quinzenal de acompanhamento.',
        ]);

        $this->get("/projetos/{$projeto->id}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projetos/Show')
                ->has('partesInteressadas', 1)
                ->where('partesInteressadas.0.nome', 'Diretora da escola')
                ->where('partesInteressadas.0.papel', 'Patrocinadora')
                ->where('partesInteressadas.0.poder', 'alto')
                ->where('partesInteressadas.0.poderFormatado', 'Alto')
                ->where('partesInteressadas.0.interesse', 'medio')
                ->where('partesInteressadas.0.interesseFormatado', 'Médio')
                ->where(
                    'partesInteressadas.0.estrategiaEngajamento',
                    'Realizar uma reunião quinzenal de acompanhamento.',
                ));
    }

    public function test_cadastra_parte_interessada_vinculada_ao_projeto(): void
    {
        $projeto = $this->criarProjeto();

        $this->post("/projetos/{$projeto->id}/partes-interessadas", [
            'nome' => 'Bibliotecária responsável',
            'papel' => 'Especialista de negócio',
            'organizacao' => 'Biblioteca Central',
            'poder' => ParteInteressada::NIVEL_MEDIO,
            'interesse' => ParteInteressada::NIVEL_ALTO,
            'estrategia_engajamento' => 'Validar os protótipos ao fim de cada etapa.',
        ])->assertRedirect("/projetos/{$projeto->id}");

        $this->assertDatabaseHas('partes_interessadas', [
            'projeto_id' => $projeto->id,
            'nome' => 'Bibliotecária responsável',
            'papel' => 'Especialista de negócio',
            'poder' => ParteInteressada::NIVEL_MEDIO,
            'interesse' => ParteInteressada::NIVEL_ALTO,
        ]);
    }

    public function test_valida_niveis_de_poder_e_interesse_ao_cadastrar(): void
    {
        $projeto = $this->criarProjeto();

        $this->from("/projetos/{$projeto->id}")
            ->post("/projetos/{$projeto->id}/partes-interessadas", [
                'nome' => 'Parte inválida',
                'poder' => 'absoluto',
                'interesse' => 'indefinido',
            ])
            ->assertRedirect("/projetos/{$projeto->id}")
            ->assertSessionHasErrors(['poder', 'interesse']);

        $this->assertDatabaseCount('partes_interessadas', 0);
    }

    public function test_atualiza_parte_interessada(): void
    {
        $projeto = $this->criarProjeto();
        $parteInteressada = $this->criarParteInteressada($projeto);

        $this->put("/projetos/{$projeto->id}/partes-interessadas/{$parteInteressada->id}", [
            'nome' => 'Coordenação pedagógica',
            'papel' => 'Aprovadora',
            'organizacao' => 'Escola Horizonte',
            'poder' => ParteInteressada::NIVEL_ALTO,
            'interesse' => ParteInteressada::NIVEL_ALTO,
            'estrategia_engajamento' => 'Apresentar decisões e impedimentos semanalmente.',
        ])->assertRedirect("/projetos/{$projeto->id}");

        $this->assertDatabaseHas('partes_interessadas', [
            'id' => $parteInteressada->id,
            'nome' => 'Coordenação pedagógica',
            'poder' => ParteInteressada::NIVEL_ALTO,
            'interesse' => ParteInteressada::NIVEL_ALTO,
            'estrategia_engajamento' => 'Apresentar decisões e impedimentos semanalmente.',
        ]);
    }

    public function test_remove_parte_interessada(): void
    {
        $projeto = $this->criarProjeto();
        $parteInteressada = $this->criarParteInteressada($projeto);

        $this->delete("/projetos/{$projeto->id}/partes-interessadas/{$parteInteressada->id}")
            ->assertRedirect("/projetos/{$projeto->id}");

        $this->assertDatabaseMissing('partes_interessadas', [
            'id' => $parteInteressada->id,
        ]);
    }

    public function test_nao_altera_parte_interessada_de_outro_projeto(): void
    {
        $projeto = $this->criarProjeto();
        $outroProjeto = $this->criarProjeto();
        $parteInteressada = $this->criarParteInteressada($outroProjeto);

        $this->put("/projetos/{$projeto->id}/partes-interessadas/{$parteInteressada->id}", [
            'nome' => 'Tentativa indevida',
            'poder' => ParteInteressada::NIVEL_ALTO,
            'interesse' => ParteInteressada::NIVEL_ALTO,
        ])->assertForbidden();

        $this->assertDatabaseHas('partes_interessadas', [
            'id' => $parteInteressada->id,
            'nome' => 'Cliente do projeto',
        ]);
    }

    public function test_aluno_responsavel_gerencia_partes_interessadas_apenas_do_proprio_projeto(): void
    {
        $aluno = User::factory()->create(['tipo' => User::TIPO_ALUNO]);
        $outroAluno = User::factory()->create(['tipo' => User::TIPO_ALUNO]);
        $projetoDoAluno = $this->criarProjeto($aluno);
        $projetoDeOutroAluno = $this->criarProjeto($outroAluno);

        $this->actingAs($aluno)
            ->post("/projetos/{$projetoDoAluno->id}/partes-interessadas", [
                'nome' => 'Cliente do aluno',
                'poder' => ParteInteressada::NIVEL_MEDIO,
                'interesse' => ParteInteressada::NIVEL_ALTO,
            ])->assertRedirect("/projetos/{$projetoDoAluno->id}");

        $this->actingAs($aluno)
            ->post("/projetos/{$projetoDeOutroAluno->id}/partes-interessadas", [
                'nome' => 'Acesso indevido',
                'poder' => ParteInteressada::NIVEL_BAIXO,
                'interesse' => ParteInteressada::NIVEL_BAIXO,
            ])->assertForbidden();

        $this->assertDatabaseCount('partes_interessadas', 1);
        $this->assertDatabaseHas('partes_interessadas', [
            'projeto_id' => $projetoDoAluno->id,
            'nome' => 'Cliente do aluno',
        ]);
    }

    private function criarProjeto(?User $responsavel = null): Projeto
    {
        $turma = Turma::create([
            'nome' => 'Gestão de Projetos',
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

    private function criarParteInteressada(Projeto $projeto): ParteInteressada
    {
        return $projeto->partesInteressadas()->create([
            'nome' => 'Cliente do projeto',
            'papel' => 'Solicitante',
            'poder' => ParteInteressada::NIVEL_MEDIO,
            'interesse' => ParteInteressada::NIVEL_ALTO,
        ]);
    }
}
