<?php

namespace App\Enums;

enum CategoriaArtefato: string
{
    case TELA = 'tela';
    case REGRA = 'regra';
    case ENTIDADE = 'entidade';
    case INTEGRACAO = 'integracao';

    public function label(): string
    {
        return match ($this) {
            self::TELA => 'Tela',
            self::REGRA => 'Regra (interface ou negócio)',
            self::ENTIDADE => 'Entidade (tabela, view)',
            self::INTEGRACAO => 'Integração (API, arquivo)',
        };
    }
}
