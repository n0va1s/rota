<?php

use App\Models\Necessidade;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

test('rota principal renderiza formulario de necessidade com sucesso', function () {
    $user = User::factory()->create();
    $produto = Produto::create([
        'nom_produto' => 'Produto Teste Form',
        'tip_tema' => 'transito',
        'tip_superintendencia' => 'suncf',
        'tip_produto' => 'api',
        'nom_gestor' => 'Luciano Fernandes',
        'eml_responsavel' => 'luciano@serpro.gov.br',
    ]);

    $response = $this->actingAs($user)->get(route('necessidade.nova', ['produto' => $produto->idt_produto]));

    $response->assertStatus(200)
        ->assertSee('Nova Necessidade')
        ->assertSee('Identificação da Necessidade');
});

test('componente necessidade-form salva nova necessidade', function () {
    $user = User::factory()->create();
    $produto = Produto::create([
        'nom_produto' => 'API de Infrações',
        'tip_tema' => 'transito',
        'tip_superintendencia' => 'suncf',
        'tip_produto' => 'api',
        'nom_gestor' => 'Luciano Fernandes',
        'eml_responsavel' => 'luciano@serpro.gov.br',
    ]);

    $this->actingAs($user);

    Volt::test('necessidade-form')
        ->set('idt_produto', $produto->idt_produto)
        ->set('des_necessidade', 'Filtro de busca por período')
        ->set('txt_descricao', 'Permitir filtrar infrações por intervalo de datas customizado.')
        ->call('salvar', false)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('necessidades', [
        'des_necessidade' => 'Filtro de busca por período',
        'idt_produto' => $produto->idt_produto,
    ]);
});

test('rota gestor-dashboard exibe necessidades e permite acao de aprovacao', function () {
    $user = User::factory()->create();
    $produto = Produto::create([
        'nom_produto' => 'Painel Teste',
        'tip_tema' => 'veiculos',
        'tip_superintendencia' => 'suncf',
        'tip_produto' => 'painel',
        'nom_gestor' => 'Gestor Teste',
        'eml_responsavel' => 'gestor@serpro.gov.br',
    ]);

    $necessidade = Necessidade::create([
        'idt_produto' => $produto->idt_produto,
        'des_necessidade' => 'Necessidade para aprovação',
        'txt_descricao' => 'Descrição completa da necessidade de teste.',
        'idt_solicitante' => $user->id,
        'tip_status' => 'em_analise',
        'ind_aprovado' => false,
        'usu_inclusao' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('gestor.dashboard'));
    $response->assertStatus(200)
        ->assertSee('Necessidade para aprovação');

    Volt::test('gestor-dashboard')
        ->call('abrirAvaliacao', $necessidade->idt_necessidade)
        ->call('salvarAvaliacao', true);

    expect($necessidade->fresh()->ind_aprovado)->toBeTrue();
});

test('mural-votacao exibe necessidades aprovadas e permite alternar votos', function () {
    $user = User::factory()->create();
    $produto = Produto::create([
        'nom_produto' => 'Painel Aprovado',
        'tip_tema' => 'veiculos',
        'tip_superintendencia' => 'suncf',
        'tip_produto' => 'painel',
        'nom_gestor' => 'Gestor Teste',
        'eml_responsavel' => 'gestor@serpro.gov.br',
    ]);

    $necessidade = Necessidade::create([
        'idt_produto' => $produto->idt_produto,
        'des_necessidade' => 'Ideia Aprovada',
        'txt_descricao' => 'Descrição de teste aprovada.',
        'idt_solicitante' => $user->id,
        'tip_status' => 'aprovada',
        'ind_aprovado' => true,
        'usu_inclusao' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->get(route('mural.votacao'));
    $response->assertStatus(200)
        ->assertSee('Ideia Aprovada');

    Volt::test('mural-votacao')
        ->call('alternarVoto', $necessidade->idt_necessidade);

    $this->assertDatabaseHas('votos', [
        'idt_necessidade' => $necessidade->idt_necessidade,
        'idt_usuario' => $user->id,
    ]);
});

test('pesquisa-satisfacao permite submeter respostas', function () {
    $user = User::factory()->create();
    $produto = Produto::create([
        'nom_produto' => 'API para Satisfação',
        'tip_tema' => 'transito',
        'tip_superintendencia' => 'suncf',
        'tip_produto' => 'api',
        'nom_gestor' => 'Luciano Fernandes',
        'eml_responsavel' => 'luciano@serpro.gov.br',
    ]);

    $this->actingAs($user);

    $response = $this->get(route('pesquisa.satisfacao'));
    $response->assertStatus(200)
        ->assertSee('Pesquisa de satisfação');

    Volt::test('pesquisa-satisfacao')
        ->set('idt_produto', $produto->idt_produto)
        ->call('submeter')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('produto_satisfacao', [
        'idt_produto' => $produto->idt_produto,
        'idt_usuario' => $user->id,
    ]);
});

test('ranking renderiza com sucesso', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('ranking'));

    $response->assertStatus(200)
        ->assertSee('Ranking de contribuidores')
        ->assertSee('Mais necessidades sugeridas');
});
