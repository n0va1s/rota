<?php

use App\Enums\StatusNecessidade;
use App\Models\Artefato;
use App\Models\Necessidade;
use App\Models\Produto;

use function Livewire\Volt\computed;
use function Livewire\Volt\state;

state([
    'filtroProduto' => '',
    'filtroStatus' => '',
    'mensagemFeed' => '',
    'idNecessidadeEmAvaliacao' => null,

    // Quantidades dos artefatos [categoria => [acao => quantidade]]
    'qtds' => [
        'tela' => ['nova' => 0, 'alteracao' => 0, 'exclusao' => 0],
        'regra' => ['nova' => 0, 'alteracao' => 0, 'exclusao' => 0],
        'entidade' => ['nova' => 0, 'alteracao' => 0, 'exclusao' => 0],
        'integracao' => ['nova' => 0, 'alteracao' => 0, 'exclusao' => 0],
    ],

    // Critérios de avaliação do gestor
    'ind_nova_oferta' => false,
    'ind_diferenciacao' => false,
    'ind_novos_clientes' => false,
    'ind_reduz_custo' => false,
    'ind_desoneracao' => false,
    'ind_urgente' => false,
    'ind_roi_alinhado' => false,
    'txt_parecer_gestor' => '',
]);

$necessidades = computed(function () {
    $query = Necessidade::with(['produto', 'solicitante', 'artefatos', 'votos']);

    if (! empty($this->filtroProduto)) {
        $query->where('idt_produto', $this->filtroProduto);
    }

    if (! empty($this->filtroStatus)) {
        $query->where('tip_status', $this->filtroStatus);
    }

    return $query->latest()->get();
});

$colunaPlanejamento = computed(fn () => $this->necessidades->filter(fn ($n) => in_array($n->tip_status->value ?? $n->tip_status, ['rascunho', 'em_analise'])));
$colunaAndamento = computed(fn () => $this->necessidades->filter(fn ($n) => in_array($n->tip_status->value ?? $n->tip_status, ['aprovada', 'em_desenvolvimento'])));
$colunaPronto = computed(fn () => $this->necessidades->filter(fn ($n) => in_array($n->tip_status->value ?? $n->tip_status, ['rejeitada', 'disponibilizada'])));

$produtos = computed(fn () => Produto::orderBy('nom_produto')->get());

$necessidadeEmAvaliacao = computed(function () {
    if (! $this->idNecessidadeEmAvaliacao) {
        return null;
    }

    return Necessidade::with(['produto', 'solicitante', 'artefatos'])->find($this->idNecessidadeEmAvaliacao);
});

$esforcoCalculado = computed(function () {
    $total = 0;
    $pesos = config('artefatos.pesos', []);

    foreach ($this->qtds as $categoria => $acoes) {
        foreach ($acoes as $acao => $qtd) {
            $peso = $pesos[$categoria][$acao] ?? 0;
            $total += max(0, (int) $qtd) * $peso;
        }
    }

    return $total;
});

$moverStatus = function (string $id, string $novoStatus) {
    $necessidade = Necessidade::findOrFail($id);
    $aprovado = in_array($novoStatus, ['aprovada', 'em_desenvolvimento', 'disponibilizada']);

    $necessidade->update([
        'tip_status' => $novoStatus,
        'ind_aprovado' => $aprovado,
        'usu_alteracao' => auth()->id(),
    ]);

    $labelStr = StatusNecessidade::tryFrom($novoStatus)?->label() ?? $novoStatus;
    $this->mensagemFeed = "Status de '{$necessidade->des_necessidade}' alterado para '{$labelStr}'.";
};

$abrirAvaliacao = function (string $id) {
    $necessidade = Necessidade::with('artefatos')->findOrFail($id);
    $this->idNecessidadeEmAvaliacao = $id;

    $novasQtds = [
        'tela' => ['nova' => 0, 'alteracao' => 0, 'exclusao' => 0],
        'regra' => ['nova' => 0, 'alteracao' => 0, 'exclusao' => 0],
        'entidade' => ['nova' => 0, 'alteracao' => 0, 'exclusao' => 0],
        'integracao' => ['nova' => 0, 'alteracao' => 0, 'exclusao' => 0],
    ];

    foreach ($necessidade->artefatos as $art) {
        $cat = $art->tip_categoria?->value ?? $art->tip_categoria;
        $acao = $art->tip_acao?->value ?? $art->tip_acao;
        if (isset($novasQtds[$cat][$acao])) {
            $novasQtds[$cat][$acao] = $art->qtd_itens;
        }
    }

    $this->qtds = $novasQtds;
    $this->ind_nova_oferta = (bool) $necessidade->ind_nova_oferta;
    $this->ind_diferenciacao = (bool) $necessidade->ind_diferenciacao;
    $this->ind_novos_clientes = (bool) $necessidade->ind_novos_clientes;
    $this->ind_reduz_custo = (bool) $necessidade->ind_reduz_custo;
    $this->ind_desoneracao = (bool) $necessidade->ind_desoneracao;
    $this->ind_urgente = (bool) $necessidade->ind_urgente;
    $this->ind_roi_alinhado = (bool) $necessidade->ind_roi_alinhado;
    $this->txt_parecer_gestor = (string) $necessidade->txt_parecer_gestor;
};

$fecharAvaliacao = function () {
    $this->idNecessidadeEmAvaliacao = null;
};

$salvarAvaliacao = function (?bool $aprovar = null) {
    if (! $this->idNecessidadeEmAvaliacao) {
        return;
    }

    $necessidade = Necessidade::findOrFail($this->idNecessidadeEmAvaliacao);

    $novoStatus = $necessidade->tip_status->value ?? $necessidade->tip_status;
    $novoAprovado = $necessidade->ind_aprovado;

    if ($aprovar === true) {
        $novoStatus = StatusNecessidade::APROVADA->value;
        $novoAprovado = true;
    } elseif ($aprovar === false) {
        $novoStatus = StatusNecessidade::REJEITADA->value;
        $novoAprovado = false;
    }

    $necessidade->update([
        'ind_aprovado' => $novoAprovado,
        'tip_status' => $novoStatus,
        'ind_nova_oferta' => $this->ind_nova_oferta,
        'ind_diferenciacao' => $this->ind_diferenciacao,
        'ind_novos_clientes' => $this->ind_novos_clientes,
        'ind_reduz_custo' => $this->ind_reduz_custo,
        'ind_desoneracao' => $this->ind_desoneracao,
        'ind_urgente' => $this->ind_urgente,
        'ind_roi_alinhado' => $this->ind_roi_alinhado,
        'txt_parecer_gestor' => $this->txt_parecer_gestor,
        'usu_alteracao' => auth()->id(),
    ]);

    Artefato::where('idt_necessidade', $necessidade->idt_necessidade)->delete();

    foreach ($this->qtds as $categoria => $acoes) {
        foreach ($acoes as $acao => $qtd) {
            $qtdInt = max(0, (int) $qtd);
            if ($qtdInt > 0) {
                Artefato::create([
                    'idt_necessidade' => $necessidade->idt_necessidade,
                    'tip_categoria' => $categoria,
                    'tip_acao' => $acao,
                    'qtd_itens' => $qtdInt,
                ]);
            }
        }
    }

    $statusMsg = match ($aprovar) {
        true => 'aprovada e avaliada',
        false => 'rejeitada',
        default => 'avaliada',
    };

    $this->mensagemFeed = "Necessidade '{$necessidade->des_necessidade}' {$statusMsg} com sucesso!";
    $this->idNecessidadeEmAvaliacao = null;
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
?>

<div class="space-y-6">
    <div class="page-header">
        <span class="eyebrow">Visão do Gestor</span>
        <h1>Kanban do Gestor</h1>
        <p>Acompanhamento e priorização do fluxo de necessidades por colunas do Kanban (Planejamento, Andamento e Pronto).</p>
    </div>

    @if ($mensagemFeed)
        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-sm font-semibold flex items-center justify-between shadow-sm" role="alert">
            <span>{{ $mensagemFeed }}</span>
            <button type="button" x-on:click="$wire.mensagemFeed = ''" class="text-indigo-700 hover:text-indigo-950 font-bold p-1">&times;</button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
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
                <flux:select.option value="">Todos os status do Kanban</flux:select.option>
                @foreach (StatusNecessidade::cases() as $st)
                    <flux:select.option value="{{ $st->value }}">{{ $st->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <!-- Quadro Kanban de 3 Colunas: Planejamento, Andamento e Pronto -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 items-start w-full">

        <!-- Coluna 1: Planejamento (Rascunho e Em Análise) -->
        <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-200/60 space-y-3 min-h-[550px]">
            <div class="flex items-center justify-between border-b border-amber-200/80 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500 shadow-xs"></span>
                    <div>
                        <h3 class="font-extrabold text-amber-950 text-xs tracking-tight uppercase">Planejamento</h3>
                        <span class="text-[10px] text-amber-800/80 font-medium">Rascunho &amp; Em análise</span>
                    </div>
                </div>
                <span class="text-xs font-extrabold bg-amber-100 text-amber-900 px-2 py-0.5 rounded-full border border-amber-200">
                    {{ $this->colunaPlanejamento->count() }}
                </span>
            </div>

            @forelse ($this->colunaPlanejamento as $necessidade)
                @include('pages.partials.kanban-card', ['necessidade' => $necessidade])
            @empty
                <div class="text-center py-12 text-amber-800/60 text-xs font-medium border-2 border-dashed border-amber-200/60 rounded-xl">
                    Nenhuma necessidade em planejamento
                </div>
            @endforelse
        </div>

        <!-- Coluna 2: Andamento (Aprovada e Em Desenvolvimento) -->
        <div class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-200/60 space-y-3 min-h-[550px]">
            <div class="flex items-center justify-between border-b border-indigo-200/80 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-indigo-600 shadow-xs"></span>
                    <div>
                        <h3 class="font-extrabold text-indigo-950 text-xs tracking-tight uppercase">Andamento</h3>
                        <span class="text-[10px] text-indigo-800/80 font-medium">Aprovada &amp; Em desenvolvimento</span>
                    </div>
                </div>
                <span class="text-xs font-extrabold bg-indigo-100 text-indigo-900 px-2 py-0.5 rounded-full border border-indigo-200">
                    {{ $this->colunaAndamento->count() }}
                </span>
            </div>

            @forelse ($this->colunaAndamento as $necessidade)
                @include('pages.partials.kanban-card', ['necessidade' => $necessidade])
            @empty
                <div class="text-center py-12 text-indigo-800/60 text-xs font-medium border-2 border-dashed border-indigo-200/60 rounded-xl">
                    Nenhuma necessidade em andamento
                </div>
            @endforelse
        </div>

        <!-- Coluna 3: Pronto (Rejeitada e Disponibilizada) -->
        <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-200/60 space-y-3 min-h-[550px]">
            <div class="flex items-center justify-between border-b border-emerald-200/80 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-600 shadow-xs"></span>
                    <div>
                        <h3 class="font-extrabold text-emerald-950 text-xs tracking-tight uppercase">Pronto</h3>
                        <span class="text-[10px] text-emerald-800/80 font-medium">Rejeitada &amp; Disponibilizada</span>
                    </div>
                </div>
                <span class="text-xs font-extrabold bg-emerald-100 text-emerald-900 px-2 py-0.5 rounded-full border border-emerald-200">
                    {{ $this->colunaPronto->count() }}
                </span>
            </div>

            @forelse ($this->colunaPronto as $necessidade)
                @include('pages.partials.kanban-card', ['necessidade' => $necessidade])
            @empty
                <div class="text-center py-12 text-emerald-800/60 text-xs font-medium border-2 border-dashed border-emerald-200/60 rounded-xl">
                    Nenhuma necessidade pronta/concluída
                </div>
            @endforelse
        </div>

    </div>

    <!-- Modal de Avaliação do Gestor (Esforço Técnico + Critérios de Valor Estratégico) -->
    @if ($this->idNecessidadeEmAvaliacao && $this->necessidadeEmAvaliacao)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full p-6 sm:p-8 space-y-6 max-h-[90vh] overflow-y-auto my-8 border border-slate-200">
                <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Avaliação do Gestor do Produto</span>
                        <h2 class="text-xl font-extrabold text-slate-900 mt-1">{{ $this->necessidadeEmAvaliacao->des_necessidade }}</h2>
                        <p class="text-xs text-slate-500 mt-1">Produto: <strong>{{ $this->necessidadeEmAvaliacao->produto->nom_produto }}</strong> | Solicitado por: {{ $this->necessidadeEmAvaliacao->solicitante->name ?? 'Usuário' }}</p>
                    </div>
                    <button type="button" wire:click="fecharAvaliacao" class="text-slate-400 hover:text-slate-600 text-2xl font-bold p-1">&times;</button>
                </div>

                <!-- Descrição do Problema de Negócio -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-sm text-slate-700">
                    <strong class="block text-xs font-extrabold uppercase text-slate-500 mb-1">Descrição da Necessidade:</strong>
                    {{ $this->necessidadeEmAvaliacao->txt_descricao }}
                </div>

                <!-- Bloco 1: Estimativa Inicial de Esforço Técnico -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <flux:icon name="cpu-chip" class="w-5 h-5 text-indigo-600" />
                            1. Estimativa Inicial de Esforço Técnico
                        </h3>
                        <x-badge variant="teal">Dimensionamento de Artefatos</x-badge>
                    </div>
                    <p class="text-xs text-slate-500">Quantifique telas, regras, entidades e integrações envolvidas nesta demanda.</p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <th scope="col" class="py-2 px-3">Item</th>
                                    <th scope="col" class="py-2 px-3 text-center">Criação</th>
                                    <th scope="col" class="py-2 px-3 text-center">Alteração</th>
                                    <th scope="col" class="py-2 px-3 text-center">Exclusão</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach (['tela' => 'Tela', 'regra' => 'Regra (interface ou negócio)', 'entidade' => 'Entidade (tabela, view)', 'integracao' => 'Integração (API, arquivo)'] as $catKey => $catLabel)
                                    <tr>
                                        <th scope="row" class="py-2 px-3 font-semibold text-slate-800 text-xs sm:text-sm">{{ $catLabel }}</th>
                                        <td class="py-1 px-3 text-center">
                                            <flux:input type="number" min="0" wire:model.live="qtds.{{ $catKey }}.nova" class="w-16 sm:w-20 text-center mx-auto" />
                                        </td>
                                        <td class="py-1 px-3 text-center">
                                            <flux:input type="number" min="0" wire:model.live="qtds.{{ $catKey }}.alteracao" class="w-16 sm:w-20 text-center mx-auto" />
                                        </td>
                                        <td class="py-1 px-3 text-center">
                                            <flux:input type="number" min="0" wire:model.live="qtds.{{ $catKey }}.exclusao" class="w-16 sm:w-20 text-center mx-auto" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 rounded-xl bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block">Pontuação Total Calculada</span>
                            <span class="text-2xl font-extrabold text-indigo-950">{{ $this->esforcoCalculado }} <span class="text-sm font-semibold text-indigo-600">pontos</span></span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                            {{ $this->esforcoCalculado }}
                        </div>
                    </div>
                </div>

                <!-- Bloco 2: Critérios de Valor Estratégico e Negócio -->
                <div class="space-y-4 pt-2">
                    <div class="border-b border-slate-100 pb-2">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <flux:icon name="chart-bar" class="w-5 h-5 text-indigo-600" />
                            2. Avaliação de Valor Estratégico &amp; Critérios de Negócio
                        </h3>
                    </div>

                    <!-- 1. Valor Estratégico e Mercado -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block">1. Valor Estratégico e Mercado</span>
                        
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <label class="text-sm font-bold text-slate-800 block">Esta necessidade viabiliza uma nova oferta comercial ou abre um novo mercado?</label>
                                <span class="text-xs text-slate-500">Ajuda a identificar inovações de alto impacto em vez de melhorias incrementais.</span>
                            </div>
                            <flux:checkbox wire:model="ind_nova_oferta" label="Sim" />
                        </div>

                        <div class="flex items-start justify-between gap-4 border-t border-slate-200/60 pt-3">
                            <div>
                                <label class="text-sm font-bold text-slate-800 block">Isso gera uma vantagem competitiva clara frente aos concorrentes atuais?</label>
                                <span class="text-xs text-slate-500">Foca na diferenciação e no valor exclusivo para o cliente.</span>
                            </div>
                            <flux:checkbox wire:model="ind_diferenciacao" label="Sim" />
                        </div>
                    </div>

                    <!-- 2. Aquisição e Retenção -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block">2. Aquisição e Retenção</span>
                        
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <label class="text-sm font-bold text-slate-800 block">Qual é o potencial de atração de novos clientes ou de retenção da base atual caso isso seja entregue?</label>
                                <span class="text-xs text-slate-500">Mede o impacto direto no crescimento do produto ou na redução de churn.</span>
                            </div>
                            <flux:checkbox wire:model="ind_novos_clientes" label="Sim" />
                        </div>
                    </div>

                    <!-- 3. Eficiência e Custos -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block">3. Eficiência e Custos</span>
                        
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <label class="text-sm font-bold text-slate-800 block">Esta entrega resulta em redução de custos operacionais diretos para a empresa ou para o cliente?</label>
                                <span class="text-xs text-slate-500">Identifica ganhos de eficiência interna ou proposta de valor voltada à economia.</span>
                            </div>
                            <flux:checkbox wire:model="ind_reduz_custo" label="Sim" />
                        </div>

                        <div class="flex items-start justify-between gap-4 border-t border-slate-200/60 pt-3">
                            <div>
                                <label class="text-sm font-bold text-slate-800 block">Amplia de alguma forma o escopo de desoneração, conformidade ou eficiência regulatória?</label>
                                <span class="text-xs text-slate-500">Especialmente relevante para mercados com regras fiscais ou governamentais complexas.</span>
                            </div>
                            <flux:checkbox wire:model="ind_desoneracao" label="Sim" />
                        </div>
                    </div>

                    <!-- 4. Viabilidade e Esforço (Priorização) -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block">4. Viabilidade e Esforço (Priorização)</span>
                        
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <label class="text-sm font-bold text-slate-800 block">Qual é a urgência ou o custo de oportunidade de não atender a essa demanda no curto prazo?</label>
                                <span class="text-xs text-slate-500">Define a criticidade temporal e risco de oportunidade.</span>
                            </div>
                            <flux:checkbox wire:model="ind_urgente" label="Urgente" />
                        </div>

                        <div class="flex items-start justify-between gap-4 border-t border-slate-200/60 pt-3">
                            <div>
                                <label class="text-sm font-bold text-slate-800 block">A estimativa de esforço gerada está alinhada ao retorno esperado (ROI) para o produto?</label>
                                <span class="text-xs text-slate-500">Cruza a estimativa técnica com o valor de negócio para decidir o Go / No-Go.</span>
                            </div>
                            <flux:checkbox wire:model="ind_roi_alinhado" label="Alinhado" />
                        </div>
                    </div>

                    <!-- Parecer do Gestor -->
                    <div>
                        <flux:textarea wire:model="txt_parecer_gestor" label="Parecer do Gestor (Justificativa Go / No-Go)" placeholder="Descreva os pontos decisivos para a avaliação e priorização..." rows="2" />
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 border-t border-slate-100 pt-4">
                    <flux:button type="button" wire:click="fecharAvaliacao" variant="filled">Cancelar</flux:button>
                    <flux:button type="button" wire:click="salvarAvaliacao(false)" variant="danger">Rejeitar Necessidade</flux:button>
                    <flux:button type="button" wire:click="salvarAvaliacao(null)" variant="filled">Salvar Rascunho de Avaliação</flux:button>
                    <flux:button type="button" wire:click="salvarAvaliacao(true)" variant="primary">Aprovar Necessidade</flux:button>
                </div>
            </div>
        </div>
    @endif
</div>
