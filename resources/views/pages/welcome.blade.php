<?php

use Livewire\Volt\Component;
use App\Models\Produto;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] #[Title('Produtos')] class extends Component {

    public ?string $filtroTema = null;

    public function with() {
        $query = Produto::orderBy('nom_produto');

        if ($this->filtroTema) {
            $query->where('tip_tema', $this->filtroTema);
        }

        return [
            'produtos' => $query->get(),
            'totalProdutos' => Produto::count(),
        ];
    }

    public function filtrarTema(?string $tema): void
    {
        $this->filtroTema = $this->filtroTema === $tema ? null : $tema;
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
        background: var(--ink);
        background-image: radial-gradient(circle at 85% 20%, rgba(192,138,46,0.18), transparent 55%);
        border-radius: 14px;
        padding: 48px 44px;
        color: #EDEFF2;
        position: relative;
        overflow: hidden;
    }

    .rota-hero .eyebrow {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: var(--amber);
        font-weight: 600;
        display: block;
        margin-bottom: 14px;
    }

    .rota-hero h1 {
        font-size: 2.6rem;
        line-height: 1.1;
        color: #fff;
        max-width: 18ch;
    }

    .rota-hero p { color: #C9D0DA; max-width: 52ch; margin-top: 14px; font-size: 0.98rem; }

    .rota-hero-count {
        position: absolute;
        top: 44px;
        right: 44px;
        text-align: right;
    }
    .rota-hero-count .n { font-family: 'DM Serif Display', serif; font-size: 2.8rem; color: var(--amber); line-height: 1; }
    .rota-hero-count .l { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; color: #9CA6B4; }

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
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(27,42,61,0.10);
    }
    .product-card .accent-bar { height: 4px; width: 100%; background: var(--teal); }
    .product-card .accent-bar.tema-transporte { background: var(--amber); }

    .product-card-body { padding: 20px 22px; display: flex; flex-direction: column; flex: 1; }

    .product-card h3 {
        font-family: 'DM Serif Display', serif;
        font-size: 1.2rem;
        color: var(--ink);
        line-height: 1.25;
    }

    .product-code {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        color: var(--ink-soft);
        background: var(--canvas);
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        margin-top: 6px;
        width: fit-content;
    }

    .badge-tipo {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 3px 9px;
        border-radius: 20px;
        background: var(--ink-2);
        color: #fff;
    }
    .badge-tema {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 3px 9px;
        border-radius: 20px;
        background: var(--teal-soft);
        color: var(--teal);
    }

    .product-meta {
        margin-top: auto;
        padding-top: 14px;
        font-size: 0.84rem;
        color: var(--ink-soft);
        border-top: 1px solid var(--line);
        margin-bottom: 16px;
    }
    .product-meta p { margin: 3px 0; }
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

    <!-- Hero -->
    <div class="rota-hero">
        <span class="eyebrow">Portfólio Rota · SERPRO</span>
        <h1 class="rota-display">O caminho de cada produto, do pedido à entrega.</h1>
        <p>Consulte o catálogo completo de produtos de trânsito, transporte, desenvolvimento urbano e meio ambiente — com acesso direto à loja e à central de ajuda de cada um.</p>

        <div class="rota-hero-count">
            <span class="n">{{ $totalProdutos }}</span>
            <span class="l">Produtos ativos</span>
        </div>

        <div class="theme-chips">
            <button type="button" wire:click="filtrarTema(null)" class="theme-chip {{ !$filtroTema ? 'active' : '' }}">
                Todos os temas
            </button>
            @foreach(\App\Enums\TipoTema::cases() ?? [] as $tema)
                <button type="button" wire:click="filtrarTema('{{ $tema->value }}')" class="theme-chip {{ $filtroTema === $tema->value ? 'active' : '' }}">
                    {{ $tema->label() }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Fluxo de processo, estilo rodovia -->
    <div class="route-flow">
        <p class="route-flow-title">Do pedido ao backlog</p>
        <div class="route-track">
            @php $steps = ['Necessidade', 'Estimativa', 'Revisão', 'Votação', 'Backlog']; @endphp
            @foreach($steps as $index => $step)
                <div class="route-stop">
                    <div class="route-marker">{{ $index + 1 }}</div>
                    <span class="route-label">{{ $step }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Grade de produtos -->
    <div class="mb-6 flex items-baseline justify-between">
        <h2 class="rota-display" style="font-size:1.5rem; color: var(--ink);">
            {{ $filtroTema ? 'Produtos filtrados' : 'Todos os produtos' }}
        </h2>
        <span class="text-sm" style="color: var(--ink-soft);">{{ $produtos->count() }} {{ Str::plural('produto', $produtos->count()) }}</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($produtos as $produto)
            <div class="product-card">
                <div class="accent-bar {{ $produto->tip_tema?->value === 'transporte' ? 'tema-transporte' : '' }}"></div>
                <div class="product-card-body">

                    <div class="flex gap-2 flex-wrap mb-3">
                        <span class="badge-tipo">{{ $produto->tip_produto?->label() ?? 'Produto' }}</span>
                        @if($produto->tip_tema)
                            <span class="badge-tema">{{ $produto->tip_tema->label() }}</span>
                        @endif
                    </div>

                    <h3>{{ $produto->nom_produto }}</h3>
                    @if($produto->cod_produto)
                        <span class="product-code">{{ $produto->cod_produto }}</span>
                    @endif

                    <div class="product-meta">
                        @if($produto->nom_gestor)
                            <p><strong>Gestor:</strong> {{ $produto->nom_gestor }}</p>
                        @endif
                        @if($produto->tip_superintendencia)
                            <p><strong>Superintendência:</strong> {{ $produto->tip_superintendencia->label() }}</p>
                        @endif
                    </div>

                    <div class="product-actions">
                        @if($produto->url_loja)
                            <a href="{{ $produto->url_loja }}" target="_blank" class="btn-route primary">
                                <flux:icon name="shopping-bag" class="w-4 h-4" /> Loja
                            </a>
                        @endif
                        @if($produto->url_central_ajuda)
                            <a href="{{ $produto->url_central_ajuda }}" target="_blank" class="btn-route secondary">
                                <flux:icon name="question-mark-circle" class="w-4 h-4" /> Ajuda
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    @if($produtos->isEmpty())
        <div class="empty-state">
            <flux:icon name="cube" class="w-10 h-10 mx-auto" style="color: var(--ink-soft);" />
            <h3>Nenhum produto neste tema</h3>
            <p>Tente outro filtro ou volte para "Todos os temas".</p>
        </div>
    @endif

</div>