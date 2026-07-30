<?php

use App\Models\Necessidade;
use App\Models\User;
use App\Services\VotacaoService;
use Livewire\Volt\Component;

new class extends Component {
    public string $mensagemFeedback = '';

    public function with(): array
    {
        return [
            'necessidadesAprovadas' => Necessidade::with(['produto', 'solicitante', 'votos.usuario'])
                ->where('ind_aprovado', true)
                ->latest()
                ->get(),
            'usuarioAutenticado' => auth()->user() ?? User::first(),
        ];
    }

    public function alternarVoto(string $idNecessidade): void
    {
        $usuario = auth()->user() ?? User::first();
        $necessidade = Necessidade::findOrFail($idNecessidade);

        /** @var VotacaoService $service */
        $service = app(VotacaoService::class);
        $votado = $service->votar($usuario, $necessidade);

        $this->mensagemFeedback = $votado
            ? 'Voto registrado com sucesso!'
            : 'Voto removido.';
    }
}; ?>

<div class="space-y-6">
    <div class="page-header">
        <span class="eyebrow">Comunidade &amp; Votação</span>
        <h1>Mural de votação</h1>
        <p>Necessidades aprovadas pelos gestores. Vote para ajudar a priorizar o backlog.</p>
    </div>

    @if ($mensagemFeedback)
        <div class="p-3.5 rounded-xl bg-teal-50 border border-teal-200 text-teal-900 text-xs font-semibold flex items-center justify-between shadow-xs" role="alert">
            <span>{{ $mensagemFeedback }}</span>
            <button type="button" x-on:click="$wire.mensagemFeedback = ''" class="text-teal-700 hover:text-teal-950 font-bold p-1">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($necessidadesAprovadas as $necessidade)
            @php
                $votadoPeloUsuario = $necessidade->votos->contains('idt_usuario', $usuarioAutenticado->id ?? null);
            @endphp
            <x-card class="flex flex-col justify-between space-y-3 w-full shadow-xs hover:shadow-sm transition-shadow rounded-xl border border-slate-200 p-4 bg-white">
                <div class="space-y-2">
                    <div class="flex items-center justify-between gap-1.5">
                        <x-badge variant="teal" size="sm">
                            {{ $necessidade->produto->tip_tema->label() ?? 'Geral' }}
                        </x-badge>
                        <span class="text-xs font-bold text-slate-600 truncate max-w-[130px]">
                            {{ $necessidade->produto->nom_produto }}
                        </span>
                    </div>

                    <h3 class="text-sm font-bold text-slate-900 leading-snug break-word-safe">{{ $necessidade->des_necessidade }}</h3>
                    <p class="text-xs text-slate-700 line-clamp-3 leading-relaxed break-word-safe">{{ $necessidade->txt_descricao }}</p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <button
                        wire:click="alternarVoto('{{ $necessidade->idt_necessidade }}')"
                        type="button"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 min-h-[44px] rounded-xl font-extrabold text-xs transition-all border {{ $votadoPeloUsuario ? 'bg-amber-600 text-white border-amber-700 shadow-xs hover:bg-amber-700' : 'bg-slate-100 text-slate-900 border-slate-300 hover:border-amber-600 hover:bg-amber-50' }}"
                        aria-pressed="{{ $votadoPeloUsuario ? 'true' : 'false' }}"
                        aria-label="{{ $votadoPeloUsuario ? 'Remover voto' : 'Votar nesta necessidade' }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                        </svg>
                        <span>{{ $votadoPeloUsuario ? 'Votado' : 'Votar' }}</span>
                    </button>

                    <span class="text-xs font-bold text-slate-700" role="status" aria-live="polite">
                        <strong class="text-slate-900 font-extrabold text-sm">{{ $necessidade->votos->count() }}</strong> {{ Str::plural('voto', $necessidade->votos->count()) }}
                    </span>
                </div>
            </x-card>
        @empty
            <x-card class="col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-4 text-center py-10 text-slate-500 text-xs">
                <p class="font-medium">Nenhuma necessidade aprovada no mural no momento.</p>
            </x-card>
        @endforelse
    </div>
</div>
