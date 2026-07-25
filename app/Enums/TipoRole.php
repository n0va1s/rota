<?php

namespace App\Enums;

enum TipoRole: string
{
    case ADMIN = 'admin';
    case GESTOR = 'gestor';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::GESTOR => 'Gestor',
            self::USER => 'Usuário',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'red',
            self::GESTOR => 'amber',
            self::USER => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ADMIN => 'shield-check',
            self::GESTOR => 'briefcase',
            self::USER => 'user',
        };
    }
}
