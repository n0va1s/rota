<?php

use App\Models\Necessidade;
use App\Models\Produto;
use App\Models\User;
use App\Services\VotacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('permite votar e alternar voto apenas em necessidades aprovadas pelo gestor', function () {
    $user = User::factory()->create();
    $produto = Produto::create([
        'nom_produto' => 'API Teste',
        'tip_tema' => 'transito',
        'tip_superintendencia' => 'suncf',
        'tip_produto' => 'api',
        'nom_gestor' => 'Gestor Teste',
        'eml_responsavel' => 'gestor@serpro.gov.br',
    ]);

    $necessidadePendente = Necessidade::create([
        'idt_produto' => $produto->idt_produto,
        'des_necessidade' => 'Necessidade Pendente',
        'txt_descricao' => 'Descrição longa de teste',
        'idt_solicitante' => $user->id,
        'tip_status' => 'em_analise',
        'ind_aprovado' => false,
        'usu_inclusao' => $user->id,
    ]);

    $service = new VotacaoService;

    // Votar em necessidade não aprovada deve lançar exceção
    expect(fn () => $service->votar($user, $necessidadePendente))
        ->toThrow(InvalidArgumentException::class);

    // Aprovar necessidade
    $necessidadePendente->update(['ind_aprovado' => true]);

    // Primeiro voto -> deve retornar true (votado)
    $resultado1 = $service->votar($user, $necessidadePendente->fresh());
    expect($resultado1)->toBeTrue();

    // Segundo voto -> toggle -> deve retornar false (removido)
    $resultado2 = $service->votar($user, $necessidadePendente->fresh());
    expect($resultado2)->toBeFalse();
});
