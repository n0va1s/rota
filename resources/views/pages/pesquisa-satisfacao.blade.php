<?php

use App\Enums\TipoCriterio;
use App\Enums\TipoProduto;
use App\Models\Produto;
use App\Models\User;
use App\Services\SatisfacaoService;
use Livewire\Volt\Component;

new class extends Component {
    public string $idt_produto = '';

    // Notas dos critérios [criterio_key => val_nota]
    public array $notas = [];

    public string $txt_frustracao = '';
    public string $txt_sugestao = '';
    public string $mensagemSucesso = '';

    public function updatedIdtProduto(): void
    {
        $this->reset(['notas', 'txt_frustracao', 'txt_sugestao', 'mensagemSucesso']);

        if ($this->idt_produto) {
            $produto = Produto::find($this->idt_produto);
            if ($produto) {
                if ($produto->tip_produto === TipoProduto::API) {
                    $this->notas = [
                        TipoCriterio::CES_FACILIDADE->value => 7,
                        TipoCriterio::CSAT_DOCUMENTACAO->value => 5,
                        TipoCriterio::CSAT_ERROS->value => 5,
                        TipoCriterio::DEV_NPS->value => 10,
                    ];
                } else {
                    $this->notas = [
                        TipoCriterio::CES_FACILIDADE->value => 7,
                        TipoCriterio::CSAT_PAINEL->value => 5,
                        TipoCriterio::NPS_PAINEL->value => 10,
                    ];
                }
            }
        }
    }

    public function with(): array
    {
        return [
            'produtos' => Produto::orderBy('nom_produto')->get(),
            'produtoSelecionado' => $this->idt_produto ? Produto::find($this->idt_produto) : null,
        ];
    }

    public function getTemNotaBaixaProperty(): bool
    {
        foreach ($this->notas as $key => $nota) {
            $criterio = TipoCriterio::tryFrom($key);
            if ($criterio && $criterio->isBaixaNota((int) $nota)) {
                return true;
            }
        }

        return false;
    }

    public function submeter(): void
    {
        $this->validate([
            'idt_produto' => 'required|exists:produtos,idt_produto',
        ], [
            'idt_produto.required' => 'Selecione o produto que deseja avaliar.',
        ]);

        if ($this->temNotaBaixa) {
            $this->validate([
                'txt_frustracao' => 'required|string|min:5',
            ], [
                'txt_frustracao.required' => 'Por favor, descreva o que causou o maior problema ou frustração durante o uso.',
                'txt_frustracao.min' => 'A justificativa deve ter ao menos 5 caracteres.',
            ]);
        }

        $usuario = auth()->user() ?? User::first();
        $produto = Produto::findOrFail($this->idt_produto);

        $avaliacoes = [];
        foreach ($this->notas as $criterioKey => $valNota) {
            $avaliacoes[] = [
                'criterio' => $criterioKey,
                'nota' => (int) $valNota,
            ];
        }

        /** @var SatisfacaoService $service */
        $service = app(SatisfacaoService::class);
        $service->registrarAvaliacao(
            $usuario,
            $produto,
            $avaliacoes,
            $this->txt_frustracao,
            $this->txt_sugestao
        );

        $this->mensagemSucesso = 'Sua pesquisa de satisfação foi registrada com sucesso! Muito obrigado pelo feedback.';
        $this->reset(['idt_produto', 'notas', 'txt_frustracao', 'txt_sugestao']);
    }
}; ?>

<div class="space-y-6">
    <div class="page-header">
        <span class="eyebrow">Qualidade &amp; Experiência</span>
        <h1>Pesquisa de satisfação</h1>
        <p>Avalie nossas APIs e Painéis para aperfeiçoar estabilidade, documentação e usabilidade.</p>
    </div>

    @if ($mensagemSucesso)
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-sm" role="alert">
            <span>{{ $mensagemSucesso }}</span>
            <button wire:click="$set('mensagemSucesso', '')" class="text-emerald-700 hover:text-emerald-950 font-bold p-1">&times;</button>
        </div>
    @endif

    <form wire:submit.prevent="submeter" class="space-y-6" novalidate>
        <x-card class="space-y-4">
            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Selecione o Produto</h2>
            <div>
                <flux:select wire:model.live="idt_produto" label="Produto *" placeholder="Escolha um produto para avaliar…">
                    @foreach ($produtos as $prod)
                        <flux:select.option value="{{ $prod->idt_produto }}">
                            {{ $prod->nom_produto }} — [{{ strtoupper($prod->tip_produto->value) }}]
                        </flux:select.option>
                    @endforeach
                </flux:select>
                @error('idt_produto') <span class="text-xs text-red-600 font-semibold mt-1 inline-block">{{ $message }}</span> @enderror
            </div>
        </x-card>

        @if ($produtoSelecionado)
            <x-card class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">{{ $produtoSelecionado->nom_produto }}</h2>
                        <span class="text-xs font-semibold text-slate-500">Gestor: {{ $produtoSelecionado->nom_gestor }}</span>
                    </div>
                    <x-badge :variant="$produtoSelecionado->tip_produto->value === 'api' ? 'primary' : 'teal'">
                        {{ $produtoSelecionado->tip_produto->label() }}
                    </x-badge>
                </div>

                @if ($produtoSelecionado->tip_produto === TipoProduto::API)
                    <!-- Questionário API -->
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-900">
                                1. O quanto foi fácil integrar e realizar a primeira chamada com sucesso na API? (CES: 1 a 7)
                            </label>
                            <div class="flex items-center gap-2 flex-wrap">
                                @for ($i = 1; $i <= 7; $i++)
                                    <button
                                        type="button"
                                        wire:click="$set('notas.{{ TipoCriterio::CES_FACILIDADE->value }}', {{ $i }})"
                                        class="w-11 h-11 rounded-xl font-bold text-sm border transition-all touch-target {{ ($notas[TipoCriterio::CES_FACILIDADE->value] ?? 0) == $i ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-200' : 'bg-slate-50 text-slate-800 border-slate-200 hover:border-indigo-600 hover:bg-indigo-50' }}"
                                    >
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-900">
                                2. Como você avalia a clareza e a precisão da documentação desta API? (CSAT: 1 a 5)
                            </label>
                            <div class="flex items-center gap-2 flex-wrap">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button
                                        type="button"
                                        wire:click="$set('notas.{{ TipoCriterio::CSAT_DOCUMENTACAO->value }}', {{ $i }})"
                                        class="w-11 h-11 rounded-xl font-bold text-sm border transition-all touch-target {{ ($notas[TipoCriterio::CSAT_DOCUMENTACAO->value] ?? 0) == $i ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-200' : 'bg-slate-50 text-slate-800 border-slate-200 hover:border-amber-500 hover:bg-amber-50' }}"
                                    >
                                        ★ {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-900">
                                3. Quando ocorrem erros, as mensagens HTTP ajudam a resolver rapidamente? (CSAT: 1 a 5)
                            </label>
                            <div class="flex items-center gap-2 flex-wrap">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button
                                        type="button"
                                        wire:click="$set('notas.{{ TipoCriterio::CSAT_ERROS->value }}', {{ $i }})"
                                        class="w-11 h-11 rounded-xl font-bold text-sm border transition-all touch-target {{ ($notas[TipoCriterio::CSAT_ERROS->value] ?? 0) == $i ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-200' : 'bg-slate-50 text-slate-800 border-slate-200 hover:border-amber-500 hover:bg-amber-50' }}"
                                    >
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-900">
                                4. O quanto você recomendaria nossas APIs para outros devs? (DevNPS: 0 a 10)
                            </label>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @for ($i = 0; $i <= 10; $i++)
                                    <button
                                        type="button"
                                        wire:click="$set('notas.{{ TipoCriterio::DEV_NPS->value }}', {{ $i }})"
                                        class="w-10 h-10 rounded-xl font-bold text-xs border transition-all touch-target {{ ($notas[TipoCriterio::DEV_NPS->value] ?? -1) == $i ? 'bg-teal-600 text-white border-teal-600 shadow-md shadow-teal-200' : 'bg-slate-50 text-slate-800 border-slate-200 hover:border-teal-600 hover:bg-teal-50' }}"
                                    >
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Questionário Painel -->
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-900">
                                1. O quanto foi fácil concluir suas tarefas no painel hoje? (CES: 1 a 7)
                            </label>
                            <div class="flex items-center gap-2 flex-wrap">
                                @for ($i = 1; $i <= 7; $i++)
                                    <button
                                        type="button"
                                        wire:click="$set('notas.{{ TipoCriterio::CES_FACILIDADE->value }}', {{ $i }})"
                                        class="w-11 h-11 rounded-xl font-bold text-sm border transition-all touch-target {{ ($notas[TipoCriterio::CES_FACILIDADE->value] ?? 0) == $i ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-200' : 'bg-slate-50 text-slate-800 border-slate-200 hover:border-indigo-600 hover:bg-indigo-50' }}"
                                    >
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-900">
                                2. Qual seu nível de satisfação com este painel de controle? (CSAT: 1 a 5)
                            </label>
                            <div class="flex items-center gap-2 flex-wrap">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button
                                        type="button"
                                        wire:click="$set('notas.{{ TipoCriterio::CSAT_PAINEL->value }}', {{ $i }})"
                                        class="w-11 h-11 rounded-xl font-bold text-sm border transition-all touch-target {{ ($notas[TipoCriterio::CSAT_PAINEL->value] ?? 0) == $i ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-200' : 'bg-slate-50 text-slate-800 border-slate-200 hover:border-amber-500 hover:bg-amber-50' }}"
                                    >
                                        ★ {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-900">
                                3. O quanto este painel facilita sua rotina de gestão no dia a dia? (NPS: 0 a 10)
                            </label>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                @for ($i = 0; $i <= 10; $i++)
                                    <button
                                        type="button"
                                        wire:click="$set('notas.{{ TipoCriterio::NPS_PAINEL->value }}', {{ $i }})"
                                        class="w-10 h-10 rounded-xl font-bold text-xs border transition-all touch-target {{ ($notas[TipoCriterio::NPS_PAINEL->value] ?? -1) == $i ? 'bg-teal-600 text-white border-teal-600 shadow-md shadow-teal-200' : 'bg-slate-50 text-slate-800 border-slate-200 hover:border-teal-600 hover:bg-teal-50' }}"
                                    >
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endif

                @if ($this->temNotaBaixa)
                    <div class="p-4 rounded-2xl bg-red-50 border border-red-200 space-y-2">
                        <x-badge variant="danger">Justificativa Obrigatória para Nota Baixa</x-badge>
                        <flux:textarea
                            wire:model="txt_frustracao"
                            label="O que causou o maior problema ou frustração durante o uso? *"
                            placeholder="Descreva detalhadamente a dificuldade encontrada..."
                            rows="3"
                        />
                        @error('txt_frustracao') <span class="text-xs text-red-600 font-semibold mt-1 inline-block">{{ $message }}</span> @enderror
                    </div>
                @else
                    <div>
                        <flux:textarea
                            wire:model="txt_sugestao"
                            label="O que você mais gosta ou o que falta para ser perfeito? (Opcional)"
                            placeholder="Deixe suas sugestões de melhoria"
                            rows="2"
                        />
                    </div>
                @endif

                <div class="pt-2">
                    <flux:button type="submit" variant="primary" class="w-full sm:w-auto font-bold py-3">Enviar Pesquisa de Satisfação</flux:button>
                </div>
            </x-card>
        @endif
    </form>
</div>
