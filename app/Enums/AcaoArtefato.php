<?php

namespace App\Enums;

enum AcaoArtefato: string
{
    case NOVA = 'nova';
    case ALTERACAO = 'alteracao';
    case EXCLUSAO = 'exclusao';

    public function label(): string
    {
        return match ($this) {
            self::NOVA => 'Nova',
            self::ALTERACAO => 'Alteração',
            self::EXCLUSAO => 'Exclusão',
        };
    }
}
