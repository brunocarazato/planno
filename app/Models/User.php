<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Modules\Turmas\Models\CadastroAluno;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    public const TIPO_ALUNO = 'aluno';

    public const TIPO_PROFESSOR = 'professor';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'ra',
        'tipo',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function aluno(): bool
    {
        return $this->tipo === self::TIPO_ALUNO;
    }

    public function professor(): bool
    {
        return $this->tipo === self::TIPO_PROFESSOR;
    }

    /**
     * @return HasMany<CadastroAluno>
     */
    public function cadastrosAlunos(): HasMany
    {
        return $this->hasMany(CadastroAluno::class);
    }

    public function possuiVinculoAprovadoDeAluno(): bool
    {
        return $this->cadastrosAlunos()
            ->where('status', CadastroAluno::STATUS_APROVADO)
            ->where(function ($query): void {
                $query
                    ->whereNull('valido_ate')
                    ->orWhereDate('valido_ate', '>=', today());
            })
            ->exists();
    }
}
