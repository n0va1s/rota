<?php

use App\Enums\AcaoArtefato;
use App\Enums\CategoriaArtefato;
use App\Enums\StatusNecessidade;
use App\Models\Artefato;
use App\Models\Necessidade;
use App\Models\Produto;
use App\Models\User;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    public string $idt_produto = '';
    public string $des_necessidade = '';
    public string $txt_descricao = '';

    // Quantidades dos artefatos [categoria => [acao => quantidade]]
    public array $qtds = [
        'tela' => ['nova' => 1, 'alteracao' => 0, 'exclusao' => 0],
        'regra' => ['nova' => 1, 'alteracao' => 0, 'exclusao' => 0],
        'entidade' => ['nova' => 1, 'alteracao' => 0, 'exclusao' => 0],
        'integracao' => ['nova' => 0, 'alteracao' => 0, 'exclusao' => 0],
    ];

    public string $mensagemSucesso = '';

    public function with(): array
    {
        return [
            'produtos' => Produto::orderBy('nom_produto')->get(),
            'usuarioAutenticado' => auth()->user() ?? User::first(),
        ];
    }

    #[Computed]
    public function esforcoTotal(): int
    {
        $total = 0;
        $pesos = config('artefatos.pesos', []);

        foreach ($this->qtds as $categoria => $acoes) {
            foreach ($acoes as $acao => $qtd) {
                $peso = $pesos[$categoria][$acao] ?? 0;
                $total += max(0, (int) $qtd) * $peso;
            }
        }

        return $total;
    }

    public function salvar(bool $rascunho = false): void
    {
        $this->validate([
            'idt_produto' => 'required|exists:produtos,idt_produto',
            'des_necessidade' => 'required|string|min:3|max:255',
            'txt_descricao' => 'required|string|min:10',
        ], [
            'idt_produto.required' => 'Selecione um produto do portfólio.',
            'des_necessidade.required' => 'Informe o título/nome da necessidade.',
            'txt_descricao.required' => 'Descreva o problema de negócio e os impactados.',
        ]);

        $usuario = auth()->user() ?? User::first();

        $necessidade = Necessidade::create([
            'idt_produto' => $this->idt_produto,
            'des_necessidade' => $this->des_necessidade,
            'txt_descricao' => $this->txt_descricao,
            'idt_solicitante' => $usuario?->id,
            'tip_status' => $rascunho ? StatusNecessidade::RASCUNHO->value : StatusNecessidade::EM_ANALISE->value,
            'ind_aprovado' => false,
            'usu_inclusao' => $usuario?->id,
        ]);

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

        $this->reset(['des_necessidade', 'txt_descricao']);
        $this->mensagemSucesso = $rascunho
            ? 'Rascunho da necessidade salvo com sucesso!'
            : 'Necessidade registrada com sucesso!';
    }
}; ?>

<div class="space-y-6">
    <div class="page-header">
        <span class="eyebrow">Novo Registro</span>
        <h1>Registrar necessidade</h1>
        <p>Descreva a necessidade vinculada a um produto do portfólio SERPRO e dimensione os componentes envolvidos.</p>
    </div>

    @if ($mensagemSucesso)
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-sm" role="alert">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>{{ $mensagemSucesso }}</span>
            </div>
            <button wire:click="$set('mensagemSucesso', '')" class="text-emerald-700 hover:text-emerald-950 font-bold p-1">&times;</button>
        </div>
    @endif

    <form wire:submit.prevent="salvar(false)" class="space-y-6" novalidate>
        <x-card class="space-y-5">
            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">1. Identificação do Produto</h2>

            <div>
                <flux:select wire:model="idt_produto" label="Produto do Portfólio *" placeholder="Selecione o produto…">
                    @foreach ($produtos as $produto)
                        <flux:select.option value="{{ $produto->idt_produto }}">
                            {{ $produto->nom_produto }} ({{ strtoupper($produto->tip_produto->value) }})
                        </flux:select.option>
                    @endforeach
                </flux:select>
                @error('idt_produto') <span class="text-xs text-red-600 font-semibold mt-1 inline-block">{{ $message }}</span> @enderror
            </div>

            <div>
                <flux:input wire:model="des_necessidade" label="Título da necessidade *" placeholder="Ex.: Exportação de relatório de infrações em lote" />
                @error('des_necessidade') <span class="text-xs text-red-600 font-semibold mt-1 inline-block">{{ $message }}</span> @enderror
            </div>

            <div>
                <flux:textarea wire:model="txt_descricao" label="Descrição detalhada *" placeholder="Descreva o problema de negócio, os impactados e o objetivo esperado" rows="3" />
                @error('txt_descricao') <span class="text-xs text-red-600 font-semibold mt-1 inline-block">{{ $message }}</span> @enderror
            </div>

            <div>
                <flux:input label="Solicitante" value="{{ $usuarioAutenticado->name ?? 'Usuário Autenticado' }}" disabled />
            </div>
        </x-card>

        <x-card class="space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-lg font-bold text-slate-900">2. Esforço Técnico Estimado</h2>
                <x-badge variant="teal">Pontuação Dinâmica</x-badge>
            </div>
            <p class="text-xs text-slate-500">Informe a quantidade de itens por tipo e ação para estimativa automática de pontos de esforço.</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th scope="col" class="py-2.5 px-3">Item</th>
                            <th scope="col" class="py-2.5 px-3 text-center">Nova</th>
                            <th scope="col" class="py-2.5 px-3 text-center">Alteração</th>
                            <th scope="col" class="py-2.5 px-3 text-center">Exclusão</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach (['tela' => 'Tela', 'regra' => 'Regra (interface ou negócio)', 'entidade' => 'Entidade (tabela, view)', 'integracao' => 'Integração (API, arquivo)'] as $catKey => $catLabel)
                            <tr>
                                <th scope="row" class="py-3 px-3 font-semibold text-slate-800 text-xs sm:text-sm">{{ $catLabel }}</th>
                                <td class="py-2 px-3 text-center">
                                    <flux:input type="number" min="0" wire:model.live="qtds.{{ $catKey }}.nova" class="w-16 sm:w-20 text-center mx-auto" />
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <flux:input type="number" min="0" wire:model.live="qtds.{{ $catKey }}.alteracao" class="w-16 sm:w-20 text-center mx-auto" />
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <flux:input type="number" min="0" wire:model.live="qtds.{{ $catKey }}.exclusao" class="w-16 sm:w-20 text-center mx-auto" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-5 rounded-2xl bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-100 flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-700 block">Esforço Calculado</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-indigo-950">{{ $this->esforcoTotal }} <span class="text-sm font-semibold text-indigo-600">pontos</span></span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-indigo-200">
                    {{ $this->esforcoTotal }}
                </div>
            </div>
        </x-card>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <flux:button type="submit" variant="primary" class="w-full sm:w-auto font-bold py-3">Enviar Necessidade</flux:button>
            <flux:button type="button" wire:click="salvar(true)" variant="filled" class="w-full sm:w-auto font-semibold py-3">Salvar Rascunho</flux:button>
        </div>
    </form>
</div>
