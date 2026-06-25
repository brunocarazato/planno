<?php

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
