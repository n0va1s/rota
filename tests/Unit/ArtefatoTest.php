<?php

use App\Enums\AcaoArtefato;
use App\Enums\CategoriaArtefato;
use App\Models\Artefato;

test('calcula pontos de artefato corretamente baseado no peso de config', function () {
    $artefatoTela = new Artefato([
        'tip_categoria' => CategoriaArtefato::TELA,
        'tip_acao' => AcaoArtefato::NOVA,
        'qtd_itens' => 2,
    ]);

    // Tela Nova = 4 pontos * 2 = 8
    expect($artefatoTela->pontos)->toBe(8);

    $artefatoEntidade = new Artefato([
        'tip_categoria' => CategoriaArtefato::ENTIDADE,
        'tip_acao' => AcaoArtefato::NOVA,
        'qtd_itens' => 3,
    ]);

    // Entidade Nova = 7 pontos * 3 = 21
    expect($artefatoEntidade->pontos)->toBe(21);
});
