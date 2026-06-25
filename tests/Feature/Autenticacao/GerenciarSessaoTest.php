<?php

namespace Tests\Feature\Autenticacao;

use App\Models\User;
use App\Modules\Turmas\Models\CadastroAluno;
use App\Modules\Turmas\Models\Turma;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GerenciarSessaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_exibe_a_tela_de_login(): void
    {
        $this->get('/entrar')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Autenticacao/Entrar'));
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
        ])->assertRedirect('/');

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
        ])->assertRedirect('/');

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

        $this->from('/entrar')
            ->post('/entrar', [
                'ra' => 'ra123',
                'password' => 'senha-segura',
            ])
            ->assertRedirect('/entrar')
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

        $this->from('/entrar')
            ->post('/entrar', [
                'ra' => 'ra123',
                'password' => 'senha-segura',
            ])
            ->assertRedirect('/entrar')
            ->assertSessionHasErrors('ra');

        $this->assertGuest();
    }

    public function test_recusa_credenciais_invalidas(): void
    {
        User::factory()->create([
            'ra' => 'RA123',
            'password' => 'senha-segura',
        ]);

        $this->from('/entrar')
            ->post('/entrar', [
                'ra' => 'RA123',
                'password' => 'senha-errada',
            ])
            ->assertRedirect('/entrar')
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
            ->assertRedirect('/entrar');

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
