<?php

namespace App\Http\Middleware;

use App\Modules\Turmas\Models\CadastroAluno;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'app' => [
                'name' => config('app.name', 'Planno'),
            ],
            'auth' => [
                'user' => fn () => $request->user()
                    ? [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'ra' => $request->user()->ra,
                        'tipo' => $request->user()->tipo,
                    ]
                    : null,
            ],
            'navegacao' => [
                'alunosAguardandoAprovacao' => fn () => $request->user()?->professor()
                    ? CadastroAluno::query()->pendentes()->count()
                    : 0,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
        ];
    }
}
