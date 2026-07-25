<?php

namespace App\Enums;

enum TipoProduto: string
{
    case API = 'api';
    case PAINEL = 'painel';

    public function label(): string
    {
        return match ($this) {
            self::API => 'API',
            self::PAINEL => 'Painel',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::API => 'indigo',
            self::PAINEL => 'amber',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::API => 'code-bracket',
            self::PAINEL => 'computer-desktop',
        };
    }
}
