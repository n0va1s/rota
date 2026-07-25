<?php

use App\Enums\StatusNecessidade;
use App\Models\Necessidade;
use App\Models\Produto;

use function Livewire\Volt\computed;
use function Livewire\Volt\state;

state([
    'filtroProduto' => '',
    'filtroStatus' => '',
    'mensagemFeed' => '',
]);

$necessidades = computed(function () {
    $query = Necessidade::with(['produto', 'solicitante', 'artefatos', 'votos']);

    if (! empty($this->filtroProduto)) {
        $query->where('idt_produto', $this->filtroProduto);
    }

    $query->where('tip_status', '!=', StatusNecessidade::RASCUNHO->value);

    if (! empty($this->filtroStatus)) {
        $query->where('tip_status', $this->filtroStatus);
    }

    return $query->latest()->get();
});

$produtos = computed(fn () => Produto::orderBy('nom_produto')->get());

$aprovar = function (string $id) {
    $necessidade = Necessidade::findOrFail($id);
    $necessidade->update([
        'ind_aprovado' => true,
        'tip_status' => StatusNecessidade::APROVADA->value,
        'usu_alteracao' => auth()->id(),
    ]);

    $this->mensagemFeed = "Necessidade '{$necessidade->des_necessidade}' aprovada com sucesso!";
};

$rejeitar = function (string $id) {
    $necessidade = Necessidade::findOrFail($id);
    $necessidade->update([
        'ind_aprovado' => false,
        'tip_status' => StatusNecessidade::REJEITADA->value,
        'usu_alteracao' => auth()->id(),
    ]);

    $this->mensagemFeed = "Necessidade '{$necessidade->des_necessidade}' rejeitada.";
};

$solicitarDetalhes = function (string $id) {
    $necessidade = Necessidade::findOrFail($id);
    $this->mensagemFeed = "Solicitação de mais detalhes enviada para o solicitante da necessidade '{$necessidade->des_necessidade}'.";
};
?>

<div class="space-y-6">
    <div class="page-header">
        <span class="eyebrow">Visão do Gestor</span>
        <h1>Dashboard do gestor</h1>
        <p>Fila de decisão de necessidades com análise de esforço e valor estratégico.</p>
    </div>

    @if ($mensagemFeed)
        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-sm font-semibold flex items-center justify-between shadow-sm" role="alert">
            <span>{{ $mensagemFeed }}</span>
            <button wire:click="$set('mensagemFeed', '')" class="text-indigo-700 hover:text-indigo-950 font-bold p-1">&times;</button>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
        <div class="flex-1">
            <flux:select wire:model.live="filtroProduto" placeholder="Todos os produtos">
                <flux:select.option value="">Todos os produtos</flux:select.option>
                @foreach ($this->produtos as $prod)
                    <flux:select.option value="{{ $prod->idt_produto }}">{{ $prod->nom_produto }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div class="w-full sm:w-64">
            <flux:select wire:model.live="filtroStatus" placeholder="Todos os status">
                <flux:select.option value="">Todos os status</flux:select.option>
                @foreach (StatusNecessidade::cases() as $st)
                    <flux:select.option value="{{ $st->value }}">{{ $st->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($this->necessidades as $necessidade)
            <x-card class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    <div class="space-y-1">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            {{ $necessidade->produto->nom_produto ?? 'Não informado' }}
                        </span>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 leading-snug">{{ $necessidade->des_necessidade }}</h3>
                        <p class="text-xs font-semibold text-slate-500">
                            Solicitado por <strong class="text-slate-800">{{ $necessidade->solicitante->name ?? 'Anônimo' }}</strong>
                        </p>
                    </div>
                    <div class="self-start">
                        @php
                            $badgeVariant = match($necessidade->tip_status->value) {
                                'aprovada' => 'teal',
                                'rejeitada' => 'danger',
                                'em_desenvolvimento' => 'primary',
                                default => 'amber',
                            };
                        @endphp
                        <x-badge :variant="$badgeVariant">
                            {{ $necessidade->tip_status->label() }}
                        </x-badge>
                    </div>
                </div>

                <p class="text-sm text-slate-600 leading-relaxed">{{ $necessidade->txt_descricao }}</p>

                <div class="grid grid-cols-3 gap-2 sm:gap-4 p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="text-center">
                        <span class="text-lg sm:text-xl font-extrabold text-slate-900 block">{{ 1 + $necessidade->votos->count() }}</span>
                        <span class="text-[10px] sm:text-xs font-semibold uppercase text-slate-500">Pedintes</span>
                    </div>
                    <div class="text-center border-x border-slate-200">
                        <span class="text-lg sm:text-xl font-extrabold text-indigo-600 block">{{ $necessidade->esforco_total }}</span>
                        <span class="text-[10px] sm:text-xs font-semibold uppercase text-slate-500">Esforço (PF)</span>
                    </div>
                    <div class="text-center">
                        <span class="text-lg sm:text-xl font-extrabold text-teal-600 block">{{ $necessidade->esforco_total > 15 ? 'Alto' : 'Médio' }}</span>
                        <span class="text-[10px] sm:text-xs font-semibold uppercase text-slate-500">Valor Est.</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                    @if ($necessidade->ind_aprovado)
                        <flux:button variant="filled" size="sm" disabled>Já aprovada</flux:button>
                        <flux:button href="{{ route('mural.votacao') }}" variant="subtle" size="sm">Ver no mural</flux:button>
                    @else
                        <flux:button wire:click="aprovar('{{ $necessidade->idt_necessidade }}')" variant="primary" size="sm">Aprovar</flux:button>
                        <flux:button wire:click="rejeitar('{{ $necessidade->idt_necessidade }}')" variant="danger" size="sm">Rejeitar</flux:button>
                        <flux:button wire:click="solicitarDetalhes('{{ $necessidade->idt_necessidade }}')" variant="subtle" size="sm">Pedir detalhes</flux:button>
                    @endif
                </div>
            </x-card>
        @empty
            <x-card class="text-center py-12 text-slate-500">
                <p class="font-medium">Nenhuma necessidade encontrada para os filtros selecionados.</p>
            </x-card>
        @endforelse
    </div>
</div>
