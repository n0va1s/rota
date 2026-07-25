<?php

namespace App\Enums;

enum TipoCriterio: string
{
    case CES_FACILIDADE = 'ces_facilidade';
    case CSAT_DOCUMENTACAO = 'csat_documentacao';
    case CSAT_ERROS = 'csat_erros';
    case DEV_NPS = 'dev_nps';
    case CSAT_PAINEL = 'csat_painel';
    case NPS_PAINEL = 'nps_painel';

    public function label(): string
    {
        return match ($this) {
            self::CES_FACILIDADE => 'Facilidade de Uso / Integração (CES)',
            self::CSAT_DOCUMENTACAO => 'Clareza e Precisão da Documentação (CSAT)',
            self::CSAT_ERROS => 'Confiabilidade e Diagnóstico de Erros (CSAT)',
            self::DEV_NPS => 'Percepção Geral do Desenvolvedor (DevNPS)',
            self::CSAT_PAINEL => 'Satisfação Geral com o Painel de Controle (CSAT)',
            self::NPS_PAINEL => 'Facilidade na Rotina de Gestão no Dia a Dia (NPS)',
        };
    }

    public function escalaMax(): int
    {
        return match ($this) {
            self::CES_FACILIDADE => 7,
            self::CSAT_DOCUMENTACAO, self::CSAT_ERROS, self::CSAT_PAINEL => 5,
            self::DEV_NPS, self::NPS_PAINEL => 10,
        };
    }

    public function isBaixaNota(int $nota): bool
    {
        return match ($this) {
            self::CES_FACILIDADE, self::CSAT_DOCUMENTACAO, self::CSAT_ERROS, self::CSAT_PAINEL => $nota <= 3,
            self::DEV_NPS, self::NPS_PAINEL => $nota <= 6,
        };
    }
}
