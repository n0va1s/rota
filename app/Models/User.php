<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\TipoRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'num_cpf', 'tip_role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

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
            'tip_role' => TipoRole::class,
        ];
    }

    public function isGestor(): bool
    {
        return $this->tip_role === TipoRole::GESTOR;
    }

    public function isAdmin(): bool
    {
        return $this->tip_role === TipoRole::ADMIN;
    }

    public function isGestorOuAdmin(): bool
    {
        return in_array($this->tip_role, [TipoRole::GESTOR, TipoRole::ADMIN], true);
    }

    public function produtosAutorizados(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'produto_autorizacao', 'user_id', 'idt_produto')
            ->withPivot('ind_gestor')
            ->withTimestamps();
    }
}
