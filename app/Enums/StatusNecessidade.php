<?php

namespace App\Enums;

enum StatusNecessidade: string
{
    case RASCUNHO = 'rascunho';
    case EM_ANALISE = 'em_analise';
    case APROVADA = 'aprovada';
    case REJEITADA = 'rejeitada';
    case EM_DESENVOLVIMENTO = 'em_desenvolvimento';
    case DISPONIBILIZADA = 'disponibilizada';

    public function label(): string
    {
        return match ($this) {
            self::RASCUNHO => 'Rascunho',
            self::EM_ANALISE => 'Em análise',
            self::APROVADA => 'Aprovada',
            self::REJEITADA => 'Rejeitada',
            self::EM_DESENVOLVIMENTO => 'Em desenvolvimento',
            self::DISPONIBILIZADA => 'Disponibilizada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RASCUNHO => 'gray',
            self::EM_ANALISE => 'amber',
            self::APROVADA => 'teal',
            self::REJEITADA => 'red',
            self::EM_DESENVOLVIMENTO => 'indigo',
            self::DISPONIBILIZADA => 'emerald',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::RASCUNHO => 'document-text',
            self::EM_ANALISE => 'clock',
            self::APROVADA => 'check-circle',
            self::REJEITADA => 'x-circle',
            self::EM_DESENVOLVIMENTO => 'cog',
            self::DISPONIBILIZADA => 'sparkles',
        };
    }
}
