<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('bottom navigation renders correctly for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('layout.bottom-navigation')
        ->assertSee('Home')
        ->assertSee('Necessidade')
        ->assertSee('Painel')
        ->assertSee('Perfil')
        ->assertSee('Sair');
});

test('bottom navigation user can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('layout.bottom-navigation')
        ->call('logout')
        ->assertRedirect('/');

    $this->assertGuest();
});
