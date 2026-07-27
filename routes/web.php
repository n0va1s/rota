<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'welcome')->name('welcome');

Route::middleware('auth')->group(function () {
    Volt::route('/necessidade/nova', 'necessidade-form')->name('necessidade.nova');
    Volt::route('/dashboard', 'gestor-dashboard')->name('gestor.dashboard');
    Volt::route('/mural', 'mural-votacao')->name('mural.votacao');
    Volt::route('/satisfacao', 'pesquisa-satisfacao')->name('pesquisa.satisfacao');
    Volt::route('/ranking', 'ranking')->name('ranking');
});
require __DIR__.'/auth.php';
