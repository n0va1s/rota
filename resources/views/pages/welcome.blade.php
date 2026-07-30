<?php

use App\Enums\TipoProduto;
use App\Enums\TipoRole;
use App\Enums\TipoSuperintendencia;
use App\Enums\TipoTema;
use App\Models\Produto;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] #[Title('Produtos')] class extends Component {
    public ?string $filtroTipoProduto = null;

    public ?string $filtroTema = null;

    public ?string $filtroSuperintendencia = null;

    // Propriedades reativas para edição inline no card
    public ?string $editingProdutoId = null;

    public string $edit_nom_produto = '';

    public ?string $edit_tip_produto = null;

    public ?string $edit_tip_tema = null;

    public ?string $edit_tip_superintendencia = null;

    public string $edit_cod_produto = '';

    public string $edit_cod_servico = '';

    public ?string $edit_idt_gestor = null;

    public ?string $edit_idt_substituto = null;

    public string $edit_url_loja = '';

    public string $edit_url_central_ajuda = '';

    public function with()
    {
        $query = Produto::with(['gestor', 'substituto', 'satisfacoes.usuario'])->orderBy('nom_produto');

        if ($this->filtroTipoProduto) {
            $query->where('tip_produto', $this->filtroTipoProduto);
        }

        if ($this->filtroTema) {
            $query->where('tip_tema', $this->filtroTema);
        }

        if ($this->filtroSuperintendencia) {
            $query->where('tip_superintendencia', $this->filtroSuperintendencia);
        }

        return [
            'produtos' => $query->get(),
            'totalProdutos' => Produto::count(),
            'tiposProduto' => TipoProduto::cases(),
            'temas' => TipoTema::cases(),
            'superintendencias' => TipoSuperintendencia::cases(),
            'gestores' => User::whereIn('tip_role', [TipoRole::GESTOR, TipoRole::ADMIN])->orderBy('name')->get(),
        ];
    }

    public function filtrarTema(?string $tema): void
    {
        $this->filtroTema = $this->filtroTema === $tema ? null : $tema;
    }

    public function limparFiltros(): void
    {
        $this->reset(['filtroTipoProduto', 'filtroTema', 'filtroSuperintendencia']);
    }

    public function iniciarEdicao(string $idt_produto): void
    {
        if (! auth()->user()?->isGestorOuAdmin()) {
            return;
        }

        $produto = Produto::findOrFail($idt_produto);

        $this->editingProdutoId = $produto->idt_produto;
        $this->edit_nom_produto = $produto->nom_produto;
        $this->edit_tip_produto = $produto->tip_produto?->value ?? '';
        $this->edit_tip_tema = $produto->tip_tema?->value ?? '';
        $this->edit_tip_superintendencia = $produto->tip_superintendencia?->value ?? '';
        $this->edit_cod_produto = $produto->cod_produto ?? '';
        $this->edit_cod_servico = $produto->cod_servico ?? '';
        $this->edit_idt_gestor = $produto->idt_gestor;
        $this->edit_idt_substituto = $produto->idt_substituto;
        $this->edit_url_loja = $produto->url_loja ?? '';
        $this->edit_url_central_ajuda = $produto->url_central_ajuda ?? '';
    }

    public function cancelarEdicao(): void
    {
        $this->reset([
            'editingProdutoId',
            'edit_nom_produto',
            'edit_tip_produto',
            'edit_tip_tema',
            'edit_tip_superintendencia',
            'edit_cod_produto',
            'edit_cod_servico',
            'edit_idt_gestor',
            'edit_idt_substituto',
            'edit_url_loja',
            'edit_url_central_ajuda',
        ]);
    }

    public function salvarEdicao(): void
    {
        if (! auth()->user()?->isGestorOuAdmin()) {
            return;
        }

        if (! $this->editingProdutoId) {
            return;
        }

        $this->validate([
            'edit_nom_produto' => 'required|string|max:255',
            'edit_tip_produto' => 'nullable|string',
            'edit_tip_tema' => 'nullable|string',
            'edit_tip_superintendencia' => 'nullable|string',
            'edit_cod_produto' => 'nullable|string|max:50',
            'edit_cod_servico' => 'nullable|string|max:50',
            'edit_idt_gestor' => 'nullable|string|exists:users,id',
            'edit_idt_substituto' => 'nullable|string|exists:users,id',
            'edit_url_loja' => 'nullable|url|max:255',
            'edit_url_central_ajuda' => 'nullable|url|max:255',
        ], [
            'edit_nom_produto.required' => 'O nome do produto é obrigatório.',
            'edit_url_loja.url' => 'O link da loja deve ser uma URL válida.',
            'edit_url_central_ajuda.url' => 'O link da central de ajuda deve ser uma URL válida.',
        ]);

        $produto = Produto::findOrFail($this->editingProdutoId);
        $produto->update([
            'nom_produto' => $this->edit_nom_produto,
            'tip_produto' => $this->edit_tip_produto ?: null,
            'tip_tema' => $this->edit_tip_tema ?: null,
            'tip_superintendencia' => $this->edit_tip_superintendencia ?: null,
            'cod_produto' => $this->edit_cod_produto ?: null,
            'cod_servico' => $this->edit_cod_servico ?: null,
            'idt_gestor' => $this->edit_idt_gestor ?: null,
            'idt_substituto' => $this->edit_idt_substituto ?: null,
            'url_loja' => $this->edit_url_loja ?: null,
            'url_central_ajuda' => $this->edit_url_central_ajuda ?: null,
        ]);

        $this->cancelarEdicao();
    }
};
?>

@php
    $fontLink = '<link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">';
@endphp

<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8" style="background: var(--canvas);">

{!! $fontLink !!}
<style>
    :root {
        --canvas: #F7F5F0;
        --surface: #FFFFFF;
        --ink: #1B2A3D;
        --ink-2: #24384F;
        --ink-soft: #55606E;
        --amber: #C08A2E;
        --amber-soft: #F1E2C4;
        --teal: #2E6E6A;
        --teal-soft: #DCEBE9;
        --line: #E3DFD6;
    }

    .rota-display { font-family: 'DM Serif Display', serif; }
    .rota-mono { font-family: 'JetBrains Mono', monospace; }

    .rota-hero {
        background: #121E2C;
        background-image: radial-gradient(circle at 85% 20%, rgba(245, 192, 86, 0.15), transparent 55%);
        border-radius: 16px;
        padding: 24px 20px;
        color: #EDEFF2;
        position: relative;
    }
    @media (min-width: 640px) {
        .rota-hero {
            padding: 44px 40px;
        }
    }

    .rota-hero .eyebrow {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: #F5C056; /* WCAG AAA 10.2:1 contraste sobre #121E2C */
        font-weight: 700;
        display: block;
        margin-bottom: 8px;
    }

    .rota-hero h1 {
        font-size: 1.65rem;
        line-height: 1.25;
        color: #ffffff;
        max-width: 22ch;
    }
    @media (min-width: 640px) {
        .rota-hero h1 {
            font-size: 2.5rem;
            line-height: 1.15;
        }
    }

    .rota-hero p {
        color: #D0D7E1;
        max-width: 54ch;
        margin-top: 14px;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .rota-hero-count {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    @media (min-width: 640px) {
        .rota-hero-count {
            align-items: flex-end;
            text-align: right;
        }
    }

    .rota-hero-count .n {
        font-family: 'DM Serif Display', serif;
        font-size: 2.2rem;
        color: #F5C056;
        line-height: 1;
    }
    @media (min-width: 640px) {
        .rota-hero-count .n {
            font-size: 3.2rem;
        }
    }

    .rota-hero-count .l {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #CBD5E1;
        font-weight: 700;
    }

    /* Chips de tema como sinalização */
    .theme-chips { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 26px; }
    .theme-chip {
        all: unset;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 7px 14px;
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.18);
        color: #D6DBE2;
    }
    .theme-chip:hover { border-color: var(--amber); color: #fff; }
    .theme-chip.active { background: var(--amber); border-color: var(--amber); color: #1B2A3D; }

    /* Fluxo de rota: linha tracejada estilo rodovia */
    .route-flow {
        margin: 44px 0 40px;
        padding: 30px 20px 10px;
        position: relative;
    }
    .route-flow-title {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--ink-soft);
        font-weight: 600;
        margin-bottom: 24px;
        text-align: center;
    }
    .route-track {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
        max-width: 760px;
        margin: 0 auto;
    }
    .route-track::before {
        content: "";
        position: absolute;
        top: 21px;
        left: 5%;
        right: 5%;
        height: 3px;
        background-image: repeating-linear-gradient(
            to right,
            var(--amber) 0, var(--amber) 10px,
            transparent 10px, transparent 20px
        );
        z-index: 0;
    }
    .route-stop {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 1;
        flex: 1;
    }
    .route-marker {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--surface);
        border: 3px solid var(--ink);
        color: var(--ink);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        font-family: 'DM Serif Display', serif;
    }
    .route-stop:first-child .route-marker { background: var(--ink); color: #fff; }
    .route-label {
        margin-top: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--ink-2);
        text-align: center;
    }

    /* Cards de produto */
    .product-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        max-width: 320px;
        width: 100%;
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(27,42,61,0.10);
    }
    .product-card .accent-bar { height: 4px; width: 100%; background: var(--teal); }
    .product-card .accent-bar.tema-transporte { background: var(--amber); }

    .product-card-body { padding: 14px 16px; display: flex; flex-direction: column; flex: 1; }

    .product-card h3 {
        font-family: 'DM Serif Display', serif;
        font-size: 1.05rem;
        color: var(--ink);
        line-height: 1.25;
    }

    .product-code {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.7rem;
        color: var(--ink-soft);
        background: var(--canvas);
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        width: fit-content;
    }

    .badge-tipo {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 2px 7px;
        border-radius: 20px;
        background: var(--ink-2);
        color: #fff;
    }
    .badge-tema {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 2px 7px;
        border-radius: 20px;
        background: var(--teal-soft);
        color: var(--teal);
    }

    .product-meta {
        margin-top: auto;
        padding-top: 10px;
        font-size: 0.78rem;
        color: var(--ink-soft);
        border-top: 1px solid var(--line);
        margin-bottom: 10px;
    }
    .product-meta p { margin: 2px 0; }
    .product-meta strong { color: var(--ink-2); }

    .product-actions { display: flex; gap: 8px; }

    .btn-route {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 9px 10px;
        border-radius: 6px;
        text-decoration: none;
        transition: background 0.15s ease;
    }
    .btn-route.primary { background: var(--ink); color: #fff; }
    .btn-route.primary:hover { background: var(--ink-2); }
    .btn-route.secondary { background: var(--canvas); color: var(--ink); border: 1px solid var(--line); }
    .btn-route.secondary:hover { background: #EFEAE0; }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--surface);
        border: 1px dashed var(--line);
        border-radius: 10px;
    }
    .empty-state h3 { font-family: 'DM Serif Display', serif; font-size: 1.3rem; color: var(--ink); margin-top: 12px; }
    .empty-state p { color: var(--ink-soft); font-size: 0.9rem; margin-top: 6px; }
</style>

    <!-- Hero Responsivo (WCAG AAA) -->
    <div class="rota-hero">
        <div class="flex flex-col-reverse sm:flex-row sm:items-start justify-between gap-4">
            <div class="space-y-1">
                <span class="eyebrow">Portfólio Rota · SERPRO</span>
                <h1 class="rota-display">O caminho de cada produto, do pedido à entrega.</h1>
            </div>

            <div class="rota-hero-count shrink-0 bg-white/5 sm:bg-transparent p-3 sm:p-0 rounded-xl border border-white/10 sm:border-0">
                <span class="n">{{ $totalProdutos }}</span>
                <span class="l">Produtos ativos</span>
            </div>
        </div>

        <p>Consulte o catálogo completo de produtos de trânsito, transporte, desenvolvimento urbano e meio ambiente — com acesso direto à loja e à central de ajuda de cada um.</p>
    </div>

    <!-- Fluxo de processo, estilo rodovia -->
    <div class="route-flow">
        <p class="route-flow-title">Do pedido ao backlog</p>
        <div class="route-track">
            @php $steps = ['Necessidade', 'Revisão', 'Votação', 'Backlog']; @endphp
            @foreach($steps as $index => $step)
                <div class="route-stop">
                    <div class="route-marker">{{ $index + 1 }}</div>
                    <span class="route-label">{{ $step }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Filtros em linha lado a lado -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <flux:icon name="funnel" class="w-4 h-4" />
                </div>
                <div>
                    <h2 class="font-extrabold text-slate-900 text-sm tracking-tight">Filtrar Portfólio</h2>
                    <span class="text-xs text-slate-500 hidden sm:inline">Refine os produtos ativos</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-1">
                <!-- Filtro por Tipo de Produto -->
                <div>
                    <flux:select wire:model.live="filtroTipoProduto" placeholder="Tipo de produto">
                        <flux:select.option value="">Todos os tipos de produto</flux:select.option>
                        @foreach ($tiposProduto as $tipo)
                            <flux:select.option value="{{ $tipo->value }}">{{ $tipo->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Filtro por Tema -->
                <div>
                    <flux:select wire:model.live="filtroTema" placeholder="Tema">
                        <flux:select.option value="">Todos os temas</flux:select.option>
                        @foreach ($temas as $tema)
                            <flux:select.option value="{{ $tema->value }}">{{ $tema->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Filtro por Superintendência -->
                <div>
                    <flux:select wire:model.live="filtroSuperintendencia" placeholder="Superintendência">
                        <flux:select.option value="">Todas as superintendências</flux:select.option>
                        @foreach ($superintendencias as $super)
                            <flux:select.option value="{{ $super->value }}">{{ $super->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            @if ($filtroTipoProduto || $filtroTema || $filtroSuperintendencia)
                <div class="shrink-0 flex items-center justify-end">
                    <button type="button" wire:click="limparFiltros" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-lg transition-colors">
                        <flux:icon name="x-mark" class="w-3.5 h-3.5" />
                        <span>Limpar</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Grade de produtos -->
    <div class="mb-6 flex items-baseline justify-between">
        <h2 class="rota-display" style="font-size:1.5rem; color: var(--ink);">
            {{ ($filtroTipoProduto || $filtroTema || $filtroSuperintendencia) ? 'Produtos filtrados' : 'Todos os produtos' }}
        </h2>
        <span class="text-sm" style="color: var(--ink-soft);">{{ $produtos->count() }} {{ Str::plural('produto', $produtos->count()) }}</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
        @foreach($produtos as $produto)
            <x-card class="flex flex-col justify-between p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-xs hover:shadow-md transition-shadow">
                <div class="accent-bar {{ $produto->tip_tema?->value === 'transporte' ? 'tema-transporte' : '' }} -mt-4 sm:-mt-5 -mx-4 sm:-mx-5 rounded-t-2xl mb-3"></div>

                @if ($editingProdutoId === $produto->idt_produto)
                    <!-- Form de Edição Inline no Card -->
                    <form wire:submit.prevent="salvarEdicao" class="space-y-3 flex-1 flex flex-col">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-1">
                            <span class="text-xs font-bold text-indigo-600 flex items-center gap-1">
                                <flux:icon name="pencil-square" class="w-3.5 h-3.5" />
                                Editando Produto
                            </span>
                        </div>

                        <div>
                            <flux:input label="Nome do Produto" wire:model="edit_nom_produto" size="sm" required />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <flux:select label="Tipo" wire:model="edit_tip_produto" size="sm">
                                    <flux:select.option value="">Selecione</flux:select.option>
                                    @foreach ($tiposProduto as $tipo)
                                        <flux:select.option value="{{ $tipo->value }}">{{ $tipo->label() }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div>
                                <flux:select label="Tema" wire:model="edit_tip_tema" size="sm">
                                    <flux:select.option value="">Selecione</flux:select.option>
                                    @foreach ($temas as $tema)
                                        <flux:select.option value="{{ $tema->value }}">{{ $tema->label() }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <flux:input label="Cód. Produto" wire:model="edit_cod_produto" size="sm" />
                            </div>
                            <div>
                                <flux:input label="Cód. Serviço" wire:model="edit_cod_servico" size="sm" />
                            </div>
                        </div>

                        <div>
                            <flux:select label="Superintendência" wire:model="edit_tip_superintendencia" size="sm">
                                <flux:select.option value="">Selecione</flux:select.option>
                                @foreach ($superintendencias as $super)
                                    <flux:select.option value="{{ $super->value }}">{{ $super->label() }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <flux:select label="Gestor" wire:model="edit_idt_gestor" size="sm">
                                    <flux:select.option value="">Sem gestor</flux:select.option>
                                    @foreach ($gestores as $g)
                                        <flux:select.option value="{{ $g->id }}">{{ $g->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div>
                                <flux:select label="Substituto" wire:model="edit_idt_substituto" size="sm">
                                    <flux:select.option value="">Sem substituto</flux:select.option>
                                    @foreach ($gestores as $g)
                                        <flux:select.option value="{{ $g->id }}">{{ $g->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </div>

                        <div>
                            <flux:input label="URL Loja" wire:model="edit_url_loja" placeholder="https://..." size="sm" />
                        </div>

                        <div>
                            <flux:input label="URL Central de Ajuda" wire:model="edit_url_central_ajuda" placeholder="https://..." size="sm" />
                        </div>

                        <div class="flex items-center gap-2 pt-3 mt-auto border-t border-slate-100">
                            <flux:button type="submit" variant="primary" size="sm" class="flex-1">
                                Salvar
                            </flux:button>
                            <flux:button type="button" variant="ghost" size="sm" wire:click="cancelarEdicao">
                                Cancelar
                            </flux:button>
                        </div>
                    </form>
                @else
                    <!-- Visualização Normal do Card -->
                    <div class="space-y-3 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <div class="flex gap-2 flex-wrap">
                                <span class="badge-tipo">{{ $produto->tip_produto?->label() ?? 'Produto' }}</span>
                                @if($produto->tip_tema)
                                    <span class="badge-tema">{{ $produto->tip_tema->label() }}</span>
                                @endif
                            </div>

                            @if (auth()->user()?->isGestorOuAdmin())
                                <button type="button" wire:click="iniciarEdicao('{{ $produto->idt_produto }}')" class="text-slate-400 hover:text-indigo-600 p-1 rounded-md hover:bg-slate-100 transition-colors" title="Editar produto">
                                    <flux:icon name="pencil-square" class="w-4 h-4" />
                                </button>
                            @endif
                        </div>

                        <h3 class="text-base font-bold text-slate-900 leading-snug break-word-safe">{{ $produto->nom_produto }}</h3>

                        <!-- Código de Produto e Código de Serviço -->
                        <div class="flex items-center gap-2 flex-wrap my-1">
                            @if($produto->cod_produto)
                                <span class="product-code" title="Código do Produto">
                                    <span class="font-normal opacity-75">Prod:</span> {{ $produto->cod_produto }}
                                </span>
                            @endif
                            @if($produto->cod_servico)
                                <span class="product-code" title="Código do Serviço">
                                    <span class="font-normal opacity-75">Serv:</span> {{ $produto->cod_servico }}
                                </span>
                            @endif
                        </div>

                        <!-- Média de Satisfação do Cliente no Card -->
                        <div class="flex items-center justify-between text-xs text-slate-700 my-2 py-2 px-3 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="font-bold text-slate-800 flex items-center gap-1">
                                <flux:icon name="star" class="w-4 h-4 text-amber-600 fill-amber-500" />
                                Satisfação:
                            </span>
                            @php
                                $media = $produto->satisfacoes->avg('val_nota');
                                $totalAval = $produto->satisfacoes->count();
                            @endphp
                            @if ($media)
                                <span class="font-extrabold text-slate-900">{{ number_format($media, 1, ',', '.') }} / 10 <span class="text-[10px] text-slate-600 font-semibold">({{ $totalAval }})</span></span>
                            @else
                                <span class="text-slate-500 italic font-medium">Sem notas</span>
                            @endif
                        </div>

                        <div class="product-meta mt-auto pt-2 border-t border-slate-100 text-xs text-slate-700 space-y-1 break-word-safe">
                            @if($produto->gestor)
                                <p><strong>Gestor:</strong> {{ $produto->gestor->name }}</p>
                            @endif
                            @if($produto->substituto)
                                <p><strong>Substituto:</strong> {{ $produto->substituto->name }}</p>
                            @endif
                            @if($produto->tip_superintendencia)
                                <p><strong>Superintendência:</strong> {{ $produto->tip_superintendencia->label() }}</p>
                            @endif
                        </div>

                        <!-- 4 Opções do Produto: Necessidade, Satisfação, Loja e Central de Ajuda (Alvos de Toque 44px+) -->
                        <div class="grid grid-cols-2 gap-2 mt-auto pt-3 border-t border-slate-100">
                            <a href="{{ route('necessidade.nova', ['produto' => $produto->idt_produto]) }}" class="flex items-center justify-center gap-1.5 px-2.5 py-2.5 min-h-[44px] rounded-xl bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-xs shadow-xs transition-colors text-center" wire:navigate>
                                <flux:icon name="plus-circle" class="w-4 h-4 shrink-0" />
                                <span>Necessidade</span>
                            </a>

                            <a href="{{ route('pesquisa.satisfacao', ['produto' => $produto->idt_produto]) }}" class="flex items-center justify-center gap-1.5 px-2.5 py-2.5 min-h-[44px] rounded-xl bg-pink-100 hover:bg-pink-200 text-pink-900 border border-pink-300 font-extrabold text-xs transition-colors text-center" wire:navigate>
                                <flux:icon name="heart" class="w-4 h-4 text-pink-700 shrink-0" />
                                <span>Satisfação</span>
                            </a>

                            @if($produto->url_loja)
                                <a href="{{ $produto->url_loja }}" target="_blank" class="flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition-colors text-center">
                                    <flux:icon name="shopping-bag" class="w-3.5 h-3.5 shrink-0" />
                                    <span>Loja</span>
                                </a>
                            @else
                                <span class="flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-slate-100 text-slate-400 font-semibold text-xs cursor-not-allowed text-center">
                                    <flux:icon name="shopping-bag" class="w-3.5 h-3.5 shrink-0 opacity-50" />
                                    <span>Loja</span>
                                </span>
                            @endif

                            @if($produto->url_central_ajuda)
                                <a href="{{ $produto->url_central_ajuda }}" target="_blank" class="flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200/80 font-semibold text-xs transition-colors text-center">
                                    <flux:icon name="question-mark-circle" class="w-3.5 h-3.5 text-teal-600 shrink-0" />
                                    <span>Central de Ajuda</span>
                                </a>
                            @else
                                <span class="flex items-center justify-center gap-1.5 px-2.5 py-2 rounded-lg bg-slate-100 text-slate-400 font-semibold text-xs cursor-not-allowed text-center">
                                    <flux:icon name="question-mark-circle" class="w-3.5 h-3.5 shrink-0 opacity-50" />
                                    <span>Central de Ajuda</span>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </x-card>
        @endforeach
    </div>

    @if($produtos->isEmpty())
        <div class="empty-state">
            <flux:icon name="cube" class="w-10 h-10 mx-auto" style="color: var(--ink-soft);" />
            <h3>Nenhum produto encontrado</h3>
            <p>Tente alterar ou limpar os filtros selecionados.</p>
        </div>
    @endif
</div>