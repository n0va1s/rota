<?php

use App\Enums\StatusNecessidade;

test('valida labels, cores e icone dos enums de status da necessidade', function () {
    expect(StatusNecessidade::EM_ANALISE->label())->toBe('Em análise')
        ->and(StatusNecessidade::EM_ANALISE->color())->toBe('amber')
        ->and(StatusNecessidade::APROVADA->label())->toBe('Aprovada')
        ->and(StatusNecessidade::APROVADA->color())->toBe('teal');
});
