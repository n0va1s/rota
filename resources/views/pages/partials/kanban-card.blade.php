@props(['necessidade'])

@php
    use App\Enums\StatusNecessidade;
    $temAvaliacao = $necessidade->ind_aprovado || $necessidade->artefatos->count() > 0 || !empty($necessidade->txt_parecer_gestor) || $necessidade->ind_nova_oferta || $necessidade->ind_urgente;
@endphp

<x-card class="space-y-2 shadow-xs hover:shadow-sm transition-shadow bg-white rounded-xl border border-slate-200/90 p-3">
    <div class="flex items-center justify-between gap-1.5">
        <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200/80 truncate max-w-[130px]">
            {{ $necessidade->produto->nom_produto ?? 'Sem produto' }}
        </span>
        <span class="text-[9px] font-bold text-slate-400 shrink-0">
            {{ $necessidade->created_at->format('d/m') }}
        </span>
    </div>

    <div>
        <h4 class="text-xs font-bold text-slate-900 leading-snug line-clamp-2">{{ $necessidade->des_necessidade }}</h4>
        <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2 leading-tight">{{ $necessidade->txt_descricao }}</p>
    </div>

    <!-- Indicadores de Esforço e Pedintes -->
    <div class="flex items-center justify-between text-[10px] bg-slate-50 p-1.5 rounded-lg border border-slate-100">
        <div>
            <span class="text-[9px] font-bold uppercase text-slate-400 block">Esforço</span>
            <span class="font-extrabold text-indigo-600">{{ $necessidade->esforco_total }} PF</span>
        </div>
        <div class="text-right">
            <span class="text-[9px] font-bold uppercase text-slate-400 block">Pedintes</span>
            <span class="font-extrabold text-slate-800">{{ 1 + $necessidade->votos->count() }}</span>
        </div>
    </div>

    <!-- Indicadores Estratégicos -->
    <div class="flex flex-wrap gap-1">
        @if ($necessidade->ind_nova_oferta)
            <span class="text-[8px] font-bold uppercase px-1 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">Oferta</span>
        @endif
        @if ($necessidade->ind_diferenciacao)
            <span class="text-[8px] font-bold uppercase px-1 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">Diferenciação</span>
        @endif
        @if ($necessidade->ind_urgente)
            <span class="text-[8px] font-bold uppercase px-1 py-0.5 rounded bg-red-50 text-red-700 border border-red-200">Urgente</span>
        @endif
        @if ($necessidade->ind_roi_alinhado)
            <span class="text-[8px] font-bold uppercase px-1 py-0.5 rounded bg-teal-50 text-teal-700 border border-teal-200">ROI</span>
        @endif
    </div>

    <!-- Ações do Card no Kanban -->
    <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between gap-1">
        @if ($temAvaliacao)
            <button
                type="button"
                wire:click="abrirAvaliacao('{{ $necessidade->idt_necessidade }}')"
                class="text-[10px] font-bold text-teal-700 hover:text-teal-900 flex items-center gap-0.5 py-1 px-1.5 rounded bg-teal-50 hover:bg-teal-100 transition-colors"
                title="Visualizar Avaliação"
            >
                <flux:icon name="eye" class="w-3 h-3 text-teal-600" />
                <span>Ver Avaliação</span>
            </button>
        @else
            <button
                type="button"
                wire:click="abrirAvaliacao('{{ $necessidade->idt_necessidade }}')"
                class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-0.5 py-1 px-1.5 rounded bg-indigo-50 hover:bg-indigo-100 transition-colors"
                title="Avaliar Necessidade"
            >
                <flux:icon name="pencil-square" class="w-3 h-3" />
                <span>Avaliar</span>
            </button>
        @endif

        <!-- Seletor Rápido de Movimentação no Kanban (Situações reais da necessidade) -->
        <select
            wire:change="moverStatus('{{ $necessidade->idt_necessidade }}', $event.target.value)"
            class="text-[10px] font-semibold bg-slate-50 border border-slate-200 text-slate-700 rounded px-1.5 py-0.5 focus:ring-1 focus:ring-indigo-500 max-w-[110px]"
        >
            @foreach (StatusNecessidade::cases() as $st)
                <option value="{{ $st->value }}" @selected(($necessidade->tip_status->value ?? $necessidade->tip_status) === $st->value)>
                    {{ $st->label() }}
                </option>
            @endforeach
        </select>
    </div>
</x-card>
