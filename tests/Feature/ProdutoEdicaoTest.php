<?php

use App\Enums\TipoRole;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

test('cabecalho exibe Acesse para visitantes e menu de perfil/sair para usuarios logados', function () {
    $guestResponse = $this->get('/');
    $guestResponse->assertOk()
        ->assertSee('Acesse');

    $user = User::factory()->create(['name' => 'Maria Silva']);
    $authResponse = $this->actingAs($user)->get('/');
    $authResponse->assertOk()
        ->assertSee('Maria Silva')
        ->assertSee('Perfil')
        ->assertSee('Sair');
});

test('componente welcome permite edicao inline de produto', function () {
    $user = User::factory()->create(['tip_role' => TipoRole::ADMIN]);
    $novoGestor = User::factory()->create(['name' => 'Novo Gestor']);

    $produto = Produto::create([
        'nom_produto' => 'Produto Original',
        'tip_tema' => 'transito',
        'tip_superintendencia' => 'suncf',
        'tip_produto' => 'api',
        'eml_responsavel' => 'gestor@serpro.gov.br',
    ]);

    $this->actingAs($user);

    Volt::test('welcome')
        ->call('iniciarEdicao', $produto->idt_produto)
        ->assertSet('editingProdutoId', $produto->idt_produto)
        ->assertSet('edit_nom_produto', 'Produto Original')
        ->set('edit_nom_produto', 'Produto Editado com Sucesso')
        ->set('edit_idt_gestor', $novoGestor->id)
        ->call('salvarEdicao')
        ->assertHasNoErrors()
        ->assertSet('editingProdutoId', null);

    $this->assertDatabaseHas('produtos', [
        'idt_produto' => $produto->idt_produto,
        'nom_produto' => 'Produto Editado com Sucesso',
        'idt_gestor' => $novoGestor->id,
    ]);
});
