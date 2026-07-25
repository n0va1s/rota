<?php

use App\Models\Necessidade;
use App\Models\User;
use App\Services\VotacaoService;
use Livewire\Volt\Component;

new class extends Component {
    public array $novosComentarios = [];
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

    public function adicionarComentario(string $idNecessidade): void
    {
        $comentario = trim($this->novosComentarios[$idNecessidade] ?? '');

        if (empty($comentario)) {
            return;
        }

        $usuario = auth()->user() ?? User::first();
        $necessidade = Necessidade::findOrFail($idNecessidade);

        /** @var VotacaoService $service */
        $service = app(VotacaoService::class);
        $service->comentar($usuario, $necessidade, $comentario);

        $this->novosComentarios[$idNecessidade] = '';
        $this->mensagemFeedback = 'Comentário adicionado com sucesso!';
    }
}; ?>

<div class="space-y-6">
    <div class="page-header">
        <span class="eyebrow">Comunidade &amp; Votação</span>
        <h1>Mural de votação</h1>
        <p>Necessidades aprovadas pelos gestores. Vote e adicione seus casos de uso para priorização.</p>
    </div>

    @if ($mensagemFeedback)
        <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200 text-teal-900 text-sm font-semibold flex items-center justify-between shadow-sm" role="alert">
            <span>{{ $mensagemFeedback }}</span>
            <button wire:click="$set('mensagemFeedback', '')" class="text-teal-700 hover:text-teal-950 font-bold p-1">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        @forelse ($necessidadesAprovadas as $necessidade)
            @php
                $votadoPeloUsuario = $necessidade->votos->contains('idt_usuario', $usuarioAutenticado->id ?? null);
            @endphp
            <x-card class="flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <x-badge variant="teal">
                            {{ $necessidade->produto->tip_tema->label() ?? 'Geral' }}
                        </x-badge>
                        <span class="text-xs font-semibold text-slate-500">
                            {{ $necessidade->produto->nom_produto }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 leading-snug">{{ $necessidade->des_necessidade }}</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $necessidade->txt_descricao }}</p>
                </div>

                <div class="space-y-4 pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between gap-3">
                        <button
                            wire:click="alternarVoto('{{ $necessidade->idt_necessidade }}')"
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition-all border touch-target {{ $votadoPeloUsuario ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-200 hover:bg-amber-600' : 'bg-slate-50 text-slate-800 border-slate-200 hover:border-amber-500 hover:bg-amber-50' }}"
                            aria-pressed="{{ $votadoPeloUsuario ? 'true' : 'false' }}"
                            aria-label="{{ $votadoPeloUsuario ? 'Remover voto' : 'Votar nesta necessidade' }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                            </svg>
                            <span>{{ $votadoPeloUsuario ? 'Votado' : 'Votar' }}</span>
                        </button>

                        <span class="text-sm font-semibold text-slate-600" role="status" aria-live="polite">
                            <strong class="text-slate-900 font-extrabold text-base">{{ $necessidade->votos->count() }}</strong> {{ Str::plural('voto', $necessidade->votos->count()) }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <flux:textarea
                            wire:model="novosComentarios.{{ $necessidade->idt_necessidade }}"
                            placeholder="Adicione um comentário ou caso de uso..."
                            rows="2"
                            aria-label="Comentário sobre {{ $necessidade->des_necessidade }}"
                        />
                        <flux:button
                            wire:click="adicionarComentario('{{ $necessidade->idt_necessidade }}')"
                            variant="filled"
                            size="sm"
                            class="w-full sm:w-auto"
                        >
                            Comentar
                        </flux:button>
                    </div>

                    @if ($necessidade->votos->whereNotNull('txt_comentario')->isNotEmpty())
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Comentários</span>
                            <ul class="space-y-2 list-none p-0 m-0">
                                @foreach ($necessidade->votos->whereNotNull('txt_comentario') as $voto)
                                    <li class="text-xs bg-slate-50 p-3 rounded-xl text-slate-800 border border-slate-100 space-y-1">
                                        <strong class="text-slate-900 font-bold block">{{ $voto->usuario->name ?? 'Usuário' }}</strong>
                                        <p class="m-0 text-slate-600 leading-normal">{{ $voto->txt_comentario }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </x-card>
        @empty
            <x-card class="col-span-1 md:col-span-2 text-center py-12 text-slate-500">
                <p class="font-medium">Nenhuma necessidade aprovada no mural no momento.</p>
            </x-card>
        @endforelse
    </div>
</div>
