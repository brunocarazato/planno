<?php

namespace Tests\Feature\GerenciamentoDeEscopo;

use App\Models\User;
use App\Modules\GerenciamentoDeEscopo\Models\DeclaracaoDeEscopo;
use App\Modules\Projetos\Models\Projeto;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GerenciarDeclaracaoDeEscopoTest extends TestCase
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

    public function test_exibe_a_declaracao_de_escopo_no_detalhe_do_projeto(): void
    {
        $projeto = $this->criarProjeto();
        $projeto->declaracaoDeEscopo()->create([
            'descricao' => '<p>Plataforma para organizar o acervo escolar.</p>',
            'inclui' => '<ul><li>Consulta de livros</li></ul>',
            'exclusoes' => '<p>Compra de novos exemplares.</p>',
        ]);

        $this->get("/projetos/{$projeto->id}/escopo")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projetos/Show')
                ->where('secao', 'escopo')
                ->where('declaracaoDeEscopo.descricao', '<p>Plataforma para organizar o acervo escolar.</p>')
                ->where('declaracaoDeEscopo.inclui', '<ul><li>Consulta de livros</li></ul>')
                ->where('declaracaoDeEscopo.exclusoes', '<p>Compra de novos exemplares.</p>'));
    }

    public function test_informa_declaracao_nula_quando_o_artefato_ainda_nao_existe(): void
    {
        $projeto = $this->criarProjeto();

        $this->get("/projetos/{$projeto->id}/escopo")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Projetos/Show')
                ->where('declaracaoDeEscopo', null));
    }

    public function test_cria_declaracao_de_escopo_sanitizando_o_conteudo(): void
    {
        $projeto = $this->criarProjeto();

        $this->post("/projetos/{$projeto->id}/declaracao-de-escopo", [
            'descricao' => '<p>Aplicação web <script>alert(1)</script> para a biblioteca.</p>',
            'inclui' => '<ul><li>Empréstimos</li><li>Devoluções</li></ul>',
            'exclusoes' => '<p>Integrações externas.</p>',
        ])->assertRedirect("/projetos/{$projeto->id}/escopo");

        $this->assertDatabaseHas('declaracoes_de_escopo', [
            'projeto_id' => $projeto->id,
            'descricao' => '<p>Aplicação web  para a biblioteca.</p>',
            'inclui' => '<ul><li>Empréstimos</li><li>Devoluções</li></ul>',
            'exclusoes' => '<p>Integrações externas.</p>',
        ]);
    }

    public function test_exige_os_tres_limites_da_declaracao(): void
    {
        $projeto = $this->criarProjeto();

        $this->from("/projetos/{$projeto->id}")
            ->post("/projetos/{$projeto->id}/declaracao-de-escopo", [])
            ->assertRedirect("/projetos/{$projeto->id}")
            ->assertSessionHasErrors(['descricao', 'inclui', 'exclusoes']);

        $this->assertDatabaseCount('declaracoes_de_escopo', 0);
    }

    public function test_atualiza_declaracao_de_escopo_existente(): void
    {
        $projeto = $this->criarProjeto();
        $declaracao = $this->criarDeclaracao($projeto);

        $this->put("/projetos/{$projeto->id}/declaracao-de-escopo/{$declaracao->id}", [
            'descricao' => '<p>Nova fronteira do produto.</p>',
            'inclui' => '<p>Catálogo e empréstimos.</p>',
            'exclusoes' => '<p>Aplicativo móvel.</p>',
        ])->assertRedirect("/projetos/{$projeto->id}/escopo");

        $this->assertDatabaseHas('declaracoes_de_escopo', [
            'id' => $declaracao->id,
            'descricao' => '<p>Nova fronteira do produto.</p>',
            'exclusoes' => '<p>Aplicativo móvel.</p>',
        ]);
    }

    public function test_nao_atualiza_declaracao_vinculada_a_outro_projeto(): void
    {
        $projeto = $this->criarProjeto();
        $outroProjeto = $this->criarProjeto();
        $declaracao = $this->criarDeclaracao($outroProjeto);

        $this->put("/projetos/{$projeto->id}/declaracao-de-escopo/{$declaracao->id}", [
            'descricao' => '<p>Tentativa indevida.</p>',
            'inclui' => '<p>Outro projeto.</p>',
            'exclusoes' => '<p>Nada.</p>',
        ])->assertForbidden();

        $this->assertDatabaseHas('declaracoes_de_escopo', [
            'id' => $declaracao->id,
            'descricao' => '<p>Escopo original.</p>',
        ]);
    }

    public function test_aluno_responsavel_gerencia_escopo_apenas_do_proprio_projeto(): void
    {
        $aluno = User::factory()->create(['tipo' => User::TIPO_ALUNO]);
        $outroAluno = User::factory()->create(['tipo' => User::TIPO_ALUNO]);
        $projetoDoAluno = $this->criarProjeto($aluno);
        $projetoDeOutroAluno = $this->criarProjeto($outroAluno);
        $dados = [
            'descricao' => '<p>Escopo do aluno.</p>',
            'inclui' => '<p>Entrega didática.</p>',
            'exclusoes' => '<p>Operação real.</p>',
        ];

        $this->actingAs($aluno)
            ->post("/projetos/{$projetoDoAluno->id}/declaracao-de-escopo", $dados)
            ->assertRedirect("/projetos/{$projetoDoAluno->id}/escopo");

        $this->actingAs($aluno)
            ->post("/projetos/{$projetoDeOutroAluno->id}/declaracao-de-escopo", $dados)
            ->assertForbidden();

        $this->assertDatabaseCount('declaracoes_de_escopo', 1);
        $this->assertDatabaseHas('declaracoes_de_escopo', [
            'projeto_id' => $projetoDoAluno->id,
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

    private function criarDeclaracao(Projeto $projeto): DeclaracaoDeEscopo
    {
        return $projeto->declaracaoDeEscopo()->create([
            'descricao' => '<p>Escopo original.</p>',
            'inclui' => '<p>Funcionalidades essenciais.</p>',
            'exclusoes' => '<p>Integrações externas.</p>',
        ]);
    }
}
