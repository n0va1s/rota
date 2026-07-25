<?php

namespace App\Enums;

enum TipoSuperintendencia: string
{
    case SUNCF = 'suncf';
    case SUNES = 'sunes';
    case SUNGD = 'sungd';

    public function label(): string
    {
        return match ($this) {
            self::SUNCF => 'SUNCF',
            self::SUNES => 'SUNES',
            self::SUNGD => 'SUNGD',
        };
    }
}
