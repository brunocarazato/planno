<?php

namespace App\Modules\Autenticacao\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Autenticacao\Http\Requests\EntrarRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessaoController extends Controller
{
    public function create(): RedirectResponse
    {
        return to_route('inicio', ['login' => 1]);
    }

    public function store(EntrarRequest $request): RedirectResponse
    {
        $credenciais = $request->validated();

        if (! Auth::attempt([
            'ra' => $credenciais['ra'],
            'password' => $credenciais['password'],
        ])) {
            return back()
                ->withErrors(['ra' => 'As credenciais informadas nao conferem.'])
                ->onlyInput('ra');
        }

        $usuario = $request->user();

        if ($usuario?->aluno() && ! $usuario->possuiVinculoAprovadoDeAluno()) {
            Auth::logout();

            return back()
                ->withErrors(['ra' => 'Seu cadastro de aluno ainda nao possui vinculo aprovado com uma turma. Entre em contato com o professor solicitando a aprovação do seu cadastro.'])
                ->onlyInput('ra');
        }

        $request->session()->regenerate();

        if ($usuario?->professor()) {
            return to_route('dashboard.professor')->with('success', 'Login realizado com sucesso.');
        }

        return to_route('inicio')->with('success', 'Login realizado com sucesso.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('inicio')->with('success', 'Voce saiu da aplicacao.');
    }
}
