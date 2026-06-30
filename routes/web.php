<?php

use App\Http\Controllers\DashboardProfessorController;
use App\Modules\Autenticacao\Http\Controllers\SessaoController;
use App\Modules\GerenciamentoDasPartesInteressadas\Http\Controllers\ParteInteressadaController;
use App\Modules\GerenciamentoDeEscopo\Http\Controllers\DeclaracaoDeEscopoController;
use App\Modules\GruposDeProcessos\Http\Controllers\TrilhaDoProjetoController;
use App\Modules\Projetos\Http\Controllers\ProjetoController;
use App\Modules\Turmas\Http\Controllers\CadastroAlunoController;
use App\Modules\Turmas\Http\Controllers\TurmaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Inicio');
})->name('inicio');

Route::get('/entrar', [SessaoController::class, 'create'])->name('login');
Route::post('/entrar', [SessaoController::class, 'store'])->name('login.store');

Route::prefix('cadastros-alunos')->name('cadastros-alunos.')->group(function (): void {
    Route::get('/solicitar', [CadastroAlunoController::class, 'create'])->name('create');
    Route::post('/', [CadastroAlunoController::class, 'store'])->name('store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/sair', [SessaoController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardProfessorController::class)
        ->middleware('professor')
        ->name('dashboard.professor');

    Route::prefix('alunos')->name('alunos.')->middleware('professor')->group(function (): void {
        Route::get('/', [CadastroAlunoController::class, 'index'])->name('index');
        Route::post('/', [CadastroAlunoController::class, 'cadastrar'])->name('store');
    });

    Route::prefix('turmas')->name('turmas.')->middleware('professor')->group(function (): void {
        Route::get('/', [TurmaController::class, 'index'])->name('index');
        Route::post('/', [TurmaController::class, 'store'])->name('store');
        Route::put('/{turma}', [TurmaController::class, 'update'])->name('update');
        Route::patch('/{turma}/permitir-cadastros', [TurmaController::class, 'permitirCadastros'])
            ->name('permitir-cadastros');
        Route::patch('/{turma}/bloquear-cadastros', [TurmaController::class, 'bloquearCadastros'])
            ->name('bloquear-cadastros');
        Route::patch('/{turma}/arquivar', [TurmaController::class, 'arquivar'])->name('arquivar');
    });

    Route::prefix('cadastros-alunos')->name('cadastros-alunos.')->middleware('professor')->group(function (): void {
        Route::patch('/{cadastroAluno}/aprovar', [CadastroAlunoController::class, 'aprovar'])->name('aprovar');
        Route::patch('/{cadastroAluno}/reprovar', [CadastroAlunoController::class, 'reprovar'])->name('reprovar');
    });

    Route::prefix('projetos')->name('projetos.')->group(function (): void {
        Route::get('/', [ProjetoController::class, 'index'])->name('index');
        Route::post('/', [ProjetoController::class, 'store'])->name('store');
        Route::get('/{projeto}', [ProjetoController::class, 'show'])->name('show');
        Route::put('/{projeto}', [ProjetoController::class, 'update'])->name('update');
        Route::patch('/{projeto}/responsavel', [ProjetoController::class, 'atualizarResponsavel'])
            ->middleware('professor')
            ->name('responsavel.update');
        Route::put('/{projeto}/termo-de-abertura', [ProjetoController::class, 'atualizarTermoDeAbertura'])
            ->name('termo-de-abertura.update');
        Route::patch('/{projeto}/trilha/atividades/{atividade}', [TrilhaDoProjetoController::class, 'atualizarConclusao'])
            ->name('trilha.atividades.update');
        Route::post('/{projeto}/partes-interessadas', [ParteInteressadaController::class, 'store'])
            ->name('partes-interessadas.store');
        Route::put('/{projeto}/partes-interessadas/{parteInteressada}', [ParteInteressadaController::class, 'update'])
            ->name('partes-interessadas.update');
        Route::delete('/{projeto}/partes-interessadas/{parteInteressada}', [ParteInteressadaController::class, 'destroy'])
            ->name('partes-interessadas.destroy');
        Route::post('/{projeto}/declaracao-de-escopo', [DeclaracaoDeEscopoController::class, 'store'])
            ->name('declaracao-de-escopo.store');
        Route::put('/{projeto}/declaracao-de-escopo/{declaracaoDeEscopo}', [DeclaracaoDeEscopoController::class, 'update'])
            ->name('declaracao-de-escopo.update');
    });
});
