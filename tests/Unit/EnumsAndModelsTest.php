<?php

use App\Enums\AcaoArtefato;
use App\Enums\CategoriaArtefato;
use App\Enums\StatusNecessidade;
use App\Enums\TipoCriterio;
use App\Enums\TipoProduto;
use App\Enums\TipoSuperintendencia;
use App\Enums\TipoTema;
use App\Models\Artefato;
use App\Models\Necessidade;
use App\Models\Produto;
use App\Models\ProdutoSatisfacao;
use App\Models\User;
use App\Models\Voto;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('testa metodos e labels de todos os enums do dominio', function () {
    expect(AcaoArtefato::NOVA->label())->toBe('Nova')
        ->and(AcaoArtefato::ALTERACAO->label())->toBe('Alteração')
        ->and(AcaoArtefato::EXCLUSAO->label())->toBe('Exclusão');

    expect(CategoriaArtefato::TELA->label())->toBe('Tela')
        ->and(CategoriaArtefato::REGRA->label())->toBe('Regra (interface ou negócio)')
        ->and(CategoriaArtefato::ENTIDADE->label())->toBe('Entidade (tabela, view)')
        ->and(CategoriaArtefato::INTEGRACAO->label())->toBe('Integração (API, arquivo)');

    expect(TipoProduto::API->label())->toBe('API')
        ->and(TipoProduto::PAINEL->label())->toBe('Painel');

    expect(TipoSuperintendencia::SUNCF->label())->toBe('SUNCF')
        ->and(TipoSuperintendencia::SUNES->label())->toBe('SUNES')
        ->and(TipoSuperintendencia::SUNGD->label())->toBe('SUNGD');

    expect(TipoTema::TRANSITO->label())->toBe('Trânsito')
        ->and(TipoTema::VEICULOS->label())->toBe('Veículos')
        ->and(TipoTema::TRANSVERSAL->label())->toBe('Transversal');

    expect(TipoCriterio::CES_FACILIDADE->label())->toBe('Facilidade de Uso / Integração (CES)')
        ->and(TipoCriterio::CSAT_DOCUMENTACAO->label())->toBe('Clareza e Precisão da Documentação (CSAT)')
        ->and(TipoCriterio::DEV_NPS->label())->toBe('Percepção Geral do Desenvolvedor (DevNPS)');

    expect(TipoCriterio::CES_FACILIDADE->isBaixaNota(3))->toBeTrue()
        ->and(TipoCriterio::DEV_NPS->isBaixaNota(6))->toBeTrue()
        ->and(TipoCriterio::DEV_NPS->isBaixaNota(9))->toBeFalse();
});

test('testa relacionamentos e accessors de models', function () {
    $user = User::factory()->create();
    $produto = Produto::create([
        'nom_produto' => 'Produto Relacionamentos',
        'tip_tema' => 'transito',
        'tip_superintendencia' => 'suncf',
        'tip_produto' => 'api',
        'nom_gestor' => 'Gestor Teste',
        'eml_responsavel' => 'gestor@test.com',
    ]);

    $necessidade = Necessidade::create([
        'idt_produto' => $produto->idt_produto,
        'des_necessidade' => 'Necessidade Teste Relacionamento',
        'txt_descricao' => 'Descrição do teste de relacionamento',
        'idt_solicitante' => $user->id,
        'tip_status' => StatusNecessidade::EM_ANALISE->value,
        'ind_aprovado' => false,
        'usu_inclusao' => $user->id,
    ]);

    Artefato::create([
        'idt_necessidade' => $necessidade->idt_necessidade,
        'tip_categoria' => CategoriaArtefato::TELA->value,
        'tip_acao' => AcaoArtefato::NOVA->value,
        'qtd_itens' => 2,
    ]);

    $voto = Voto::create([
        'idt_necessidade' => $necessidade->idt_necessidade,
        'idt_usuario' => $user->id,
        'txt_comentario' => 'Muito bom!',
    ]);

    $satisfacao = ProdutoSatisfacao::create([
        'idt_produto' => $produto->idt_produto,
        'idt_usuario' => $user->id,
        'tip_criterio' => TipoCriterio::CES_FACILIDADE->value,
        'val_nota' => 7,
    ]);

    expect($necessidade->produto->idt_produto)->toBe($produto->idt_produto)
        ->and($necessidade->solicitante->id)->toBe($user->id)
        ->and($necessidade->artefatos->count())->toBe(1)
        ->and($necessidade->votos->count())->toBe(1)
        ->and($voto->necessidade->idt_necessidade)->toBe($necessidade->idt_necessidade)
        ->and($voto->usuario->id)->toBe($user->id)
        ->and($satisfacao->produto->idt_produto)->toBe($produto->idt_produto)
        ->and($satisfacao->usuario->id)->toBe($user->id)
        ->and($produto->necessidades->count())->toBe(1)
        ->and($produto->satisfacoes->count())->toBe(1);
});
