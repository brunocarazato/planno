<?php

use App\Modules\Projetos\Http\Controllers\ProjetoController;
use App\Modules\Turmas\Http\Controllers\CadastroAlunoController;
use App\Modules\Turmas\Http\Controllers\TurmaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Inicio');
})->name('inicio');

Route::prefix('turmas')->name('turmas.')->group(function (): void {
    Route::get('/', [TurmaController::class, 'index'])->name('index');
    Route::post('/', [TurmaController::class, 'store'])->name('store');
    Route::put('/{turma}', [TurmaController::class, 'update'])->name('update');
    Route::patch('/{turma}/permitir-cadastros', [TurmaController::class, 'permitirCadastros'])
        ->name('permitir-cadastros');
    Route::patch('/{turma}/bloquear-cadastros', [TurmaController::class, 'bloquearCadastros'])
        ->name('bloquear-cadastros');
    Route::patch('/{turma}/arquivar', [TurmaController::class, 'arquivar'])->name('arquivar');
});

Route::prefix('cadastros-alunos')->name('cadastros-alunos.')->group(function (): void {
    Route::get('/solicitar', [CadastroAlunoController::class, 'create'])->name('create');
    Route::post('/', [CadastroAlunoController::class, 'store'])->name('store');
    Route::patch('/{cadastroAluno}/aprovar', [CadastroAlunoController::class, 'aprovar'])->name('aprovar');
    Route::patch('/{cadastroAluno}/reprovar', [CadastroAlunoController::class, 'reprovar'])->name('reprovar');
});

Route::prefix('projetos')->name('projetos.')->group(function (): void {
    Route::get('/', [ProjetoController::class, 'index'])->name('index');
    Route::post('/', [ProjetoController::class, 'store'])->name('store');
    Route::get('/{projeto}', [ProjetoController::class, 'show'])->name('show');
    Route::put('/{projeto}/termo-de-abertura', [ProjetoController::class, 'atualizarTermoDeAbertura'])
        ->name('termo-de-abertura.update');
});
