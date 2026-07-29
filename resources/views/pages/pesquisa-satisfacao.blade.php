<?php

use App\Enums\TipoCriterio;
use App\Enums\TipoProduto;
use App\Models\Produto;
use App\Models\ProdutoSatisfacao;
use App\Models\User;
use App\Services\SatisfacaoService;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new class extends Component {
    #[Url]
    public ?string $produto = null;

    public string $idt_produto = '';

    // Quantidades/notas dos critérios [criterio_key => val_nota]
    public array $notas = [];

    public string $txt_frustracao = '';

    public string $txt_sugestao = '';

    public string $mensagemSucesso = '';

    public bool $exibirFormularioAvaliacao = false;

    public function mount(?string $produto = null): void
    {
        $produtoId = $produto ?? $this->produto ?? request()->query('produto');
        if ($produtoId && Produto::find($produtoId)) {
            $this->idt_produto = $produtoId;
            $this->updatedIdtProduto();
        }
    }

    public function updatedIdtProduto(): void
    {
        $this->reset(['notas', 'txt_frustracao', 'txt_sugestao', 'mensagemSucesso', 'exibirFormularioAvaliacao']);

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
        $usuario = auth()->user() ?? User::first();

        $minhaAvaliacao = ($this->idt_produto && $usuario)
            ? ProdutoSatisfacao::where('idt_produto', $this->idt_produto)
                ->where('idt_usuario', $usuario->id)
                ->latest()
                ->first()
            : null;

        $ultimasCincoAvaliacoes = $this->idt_produto
            ? ProdutoSatisfacao::with('usuario')
                ->where('idt_produto', $this->idt_produto)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return [
            'produtos' => Produto::orderBy('nom_produto')->get(),
            'produtoSelecionado' => $this->idt_produto ? Produto::with(['gestor', 'satisfacoes.usuario'])->find($this->idt_produto) : null,
            'minhaAvaliacao' => $minhaAvaliacao,
            'ultimasCincoAvaliacoes' => $ultimasCincoAvaliacoes,
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
        if (! $this->idt_produto) {
            return;
        }

        $produto = Produto::findOrFail($this->idt_produto);
        $usuario = auth()->user() ?? User::first();

        $avaliacoes = [];
        foreach ($this->notas as $key => $val) {
            $criterio = TipoCriterio::from($key);
            $avaliacoes[] = [
                'criterio' => $criterio,
                'nota' => (int) $val,
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
        $this->exibirFormularioAvaliacao = false;
        $this->reset(['notas', 'txt_frustracao', 'txt_sugestao']);
        $this->updatedIdtProduto();
    }
}; ?>

<div class="space-y-6">
    <!-- Breadcrumb de Navegação -->
    <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 bg-white px-4 py-2.5 rounded-xl border border-slate-200 shadow-xs" aria-label="Breadcrumb">
        <a href="{{ route('welcome') }}" class="hover:text-indigo-600 font-semibold transition-colors flex items-center gap-1.5 text-slate-600" wire:navigate>
            <flux:icon name="home" class="w-4 h-4 text-slate-400" />
            <span>Home</span>
        </a>
        <flux:icon name="chevron-right" class="w-3.5 h-3.5 text-slate-300" />
        @if ($produtoSelecionado)
            <span class="font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 text-xs">
                {{ $produtoSelecionado->nom_produto }}
            </span>
            <flux:icon name="chevron-right" class="w-3.5 h-3.5 text-slate-300" />
        @endif
        <span class="font-extrabold text-indigo-600">Pesquisa de Satisfação</span>
    </nav>

    <div class="page-header">
        <span class="eyebrow">Qualidade &amp; Experiência</span>
        <h1>Pesquisa de satisfação</h1>
        <p>Avaliação da experiência do usuário para o produto selecionado.</p>
    </div>

    @if (! $produtoSelecionado)
        <!-- Alerta de Produto Não Selecionado -->
        <x-card class="p-8 text-center space-y-4 max-w-lg mx-auto bg-amber-50/80 border border-amber-200 rounded-2xl shadow-sm my-6">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto font-bold text-xl">
                !
            </div>
            <div class="space-y-2">
                <h2 class="text-lg font-bold text-amber-950">Nenhum produto selecionado</h2>
                <p class="text-sm text-amber-900 leading-relaxed font-medium">
                    Selecione o produto para responder à pesquisa de satisfação.
                </p>
            </div>
            <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm shadow-sm transition-colors w-full sm:w-auto" wire:navigate>
                <flux:icon name="arrow-left" class="w-4 h-4" />
                <span>Voltar&nbsp;</span>
            </a>
        </x-card>
    @else
        @if ($mensagemSucesso)
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-sm" role="alert">
                <span>{{ $mensagemSucesso }}</span>
                <button type="button" x-on:click="$wire.mensagemSucesso = ''" class="text-emerald-700 hover:text-emerald-950 font-bold p-1">&times;</button>
            </div>
        @endif

        <!-- Painel do Produto Fixado -->
        <x-card class="space-y-4 shadow-sm border border-indigo-100 bg-white">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">
                            Produto Fixado da Home
                        </span>
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-900">{{ $produtoSelecionado->nom_produto }}</h2>
                    <p class="text-xs text-slate-500 font-medium">Gestor: {{ $produtoSelecionado->gestor?->name ?? 'Não informado' }} | Tipo: {{ strtoupper($produtoSelecionado->tip_produto->value) }}</p>
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $media = $produtoSelecionado->satisfacoes->avg('val_nota');
                        $totalAval = $produtoSelecionado->satisfacoes->count();
                    @endphp
                    <div class="text-right">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Nota Média</span>
                        <span class="text-2xl font-extrabold text-indigo-950">
                            @if($media) {{ number_format($media, 1, ',', '.') }} <span class="text-sm font-semibold text-slate-500">/ 10</span> @else -- @endif
                        </span>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                        ★
                    </div>
                </div>
            </div>

            <!-- Formulário de Avaliação Direto -->
            <form wire:submit.prevent="submeter" class="space-y-6 pt-2">
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Critérios de Avaliação</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($notas as $criterioKey => $notaValor)
                            @php
                                $criterioEnum = TipoCriterio::tryFrom($criterioKey);
                            @endphp
                            @if ($criterioEnum)
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="text-sm font-bold text-slate-800">{{ $criterioEnum->label() }}</label>
                                        <span class="text-sm font-extrabold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">
                                            {{ $notas[$criterioKey] }} / 10
                                        </span>
                                    </div>
                                    <input
                                        type="range"
                                        min="0"
                                        max="10"
                                        step="1"
                                        wire:model.live="notas.{{ $criterioKey }}"
                                        class="w-full accent-indigo-600 cursor-pointer"
                                    />
                                    <div class="flex justify-between text-[10px] text-slate-400 font-semibold">
                                        <span>0 (Ruim)</span>
                                        <span>5 (Regular)</span>
                                        <span>10 (Excelente)</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Feedbacks Qualitativos -->
                <div class="space-y-4 pt-2 border-t border-slate-100">
                    <div>
                        <flux:textarea wire:model="txt_frustracao" label="Pontos de Frustração / Dificuldades" placeholder="Relate se encontrou erros, lentidão ou falta de documentação..." rows="2" />
                    </div>
                    <div>
                        <flux:textarea wire:model="txt_sugestao" label="Sugestões de Melhoria" placeholder="O que podemos fazer para elevar a nota deste produto?" rows="2" />
                    </div>
                </div>

                <div class="pt-2">
                    <flux:button type="submit" variant="primary" class="w-full sm:w-auto font-bold py-3">
                        Enviar Pesquisa de Satisfação
                    </flux:button>
                </div>
            </form>
        </x-card>

        <!-- 5 Avaliações Mais Recentes deste Produto -->
        @if ($ultimasCincoAvaliacoes->isNotEmpty())
            <x-card class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span>Últimas 5 Avaliações de Usuários</span>
                    <span class="text-xs font-semibold text-slate-500">{{ $ultimasCincoAvaliacoes->count() }} registradas</span>
                </h3>

                <div class="space-y-3">
                    @foreach ($ultimasCincoAvaliacoes as $aval)
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">{{ $aval->usuario->name ?? 'Usuário' }}</span>
                                <span class="text-slate-400 text-[10px]">{{ $aval->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">
                                    Nota: {{ number_format($aval->val_nota, 1, ',', '.') }}
                                </span>
                            </div>
                            @if ($aval->txt_frustracao)
                                <p class="text-slate-600"><strong class="text-slate-700">Frustração:</strong> {{ $aval->txt_frustracao }}</p>
                            @endif
                            @if ($aval->txt_sugestao)
                                <p class="text-slate-600"><strong class="text-slate-700">Sugestão:</strong> {{ $aval->txt_sugestao }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif
    @endif
</div>
