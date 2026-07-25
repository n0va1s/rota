<?php

use App\Enums\TipoCriterio;
use App\Models\Produto;
use App\Models\User;
use App\Services\SatisfacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registra pesquisa de satisfacao normalizada e exige texto de frustracao se nota for baixa', function () {
    $user = User::factory()->create();
    $produto = Produto::create([
        'nom_produto' => 'Painel Teste',
        'tip_tema' => 'transito',
        'tip_superintendencia' => 'suncf',
        'tip_produto' => 'painel',
        'nom_gestor' => 'Gestor Teste',
        'eml_responsavel' => 'gestor@serpro.gov.br',
    ]);

    $service = new SatisfacaoService();

    // Nota baixa sem justificativa de frustração deve lançar exceção
    $avaliacoesComNotaBaixa = [
        ['criterio' => TipoCriterio::CSAT_PAINEL->value, 'nota' => 2],
    ];

    expect(fn () => $service->registrarAvaliacao($user, $produto, $avaliacoesComNotaBaixa, null))
        ->toThrow(InvalidArgumentException::class);

    // Com justificativa de frustração -> deve salvar com sucesso
    $registros = $service->registrarAvaliacao(
        $user,
        $produto,
        $avaliacoesComNotaBaixa,
        'O painel apresentou lentidão excessiva no carregamento'
    );

    expect($registros)->toHaveCount(1)
        ->and($registros->first()->val_nota)->toBe(2)
        ->and($registros->first()->txt_frustracao)->toBe('O painel apresentou lentidão excessiva no carregamento');
});
