<?php

namespace App\Enums;

enum TipoTema: string
{
    case TRANSITO = 'transito';
    case TRANSPORTE = 'transporte';
    case DESENVOLVIMENTO_URBANO = 'desenvolvimento_urbano';
    case MEIO_AMBIENTE = 'meio_ambiente';
    case CONDUTORES = 'condutores';
    case VEICULOS = 'veiculos';
    case TRANSVERSAL = 'transversal';

    public function label(): string
    {
        return match ($this) {
            self::TRANSITO => 'Trânsito',
            self::TRANSPORTE => 'Transporte',
            self::DESENVOLVIMENTO_URBANO => 'Desenvolvimento Urbano',
            self::MEIO_AMBIENTE => 'Meio Ambiente',
            self::CONDUTORES => 'Condutores',
            self::VEICULOS => 'Veículos',
            self::TRANSVERSAL => 'Transversal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TRANSITO, self::VEICULOS => 'teal',
            self::TRANSPORTE, self::CONDUTORES => 'indigo',
            self::DESENVOLVIMENTO_URBANO, self::TRANSVERSAL => 'amber',
            self::MEIO_AMBIENTE => 'emerald',
        };
    }
}
