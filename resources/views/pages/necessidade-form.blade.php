<?php

use App\Enums\StatusNecessidade;
use App\Models\Necessidade;
use App\Models\Produto;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new class extends Component {
    #[Url]
    public ?string $produto = null;

    public string $idt_produto = '';

    public ?Produto $produtoModel = null;

    public string $des_necessidade = '';

    public string $txt_descricao = '';

    public string $mensagemSucesso = '';

    public function mount(?string $produto = null): void
    {
        $produtoId = $produto ?? $this->produto ?? request()->query('produto');
        if ($produtoId) {
            $this->produtoModel = Produto::find($produtoId);
            if ($this->produtoModel) {
                $this->idt_produto = $this->produtoModel->idt_produto;
            }
        }
    }

    public function salvar(bool $rascunho = false): void
    {
        $this->validate([
            'idt_produto' => 'required|exists:produtos,idt_produto',
            'des_necessidade' => 'required|string|min:3|max:255',
            'txt_descricao' => 'required|string|min:10',
        ], [
            'idt_produto.required' => 'Um produto do portfólio deve ser selecionado.',
            'des_necessidade.required' => 'Informe o título/nome da necessidade.',
            'txt_descricao.required' => 'Descreva o problema de negócio e os impactados.',
        ]);

        $usuario = auth()->user() ?? User::first();

        Necessidade::create([
            'idt_produto' => $this->idt_produto,
            'des_necessidade' => $this->des_necessidade,
            'txt_descricao' => $this->txt_descricao,
            'idt_solicitante' => $usuario?->id,
            'tip_status' => $rascunho ? StatusNecessidade::RASCUNHO->value : StatusNecessidade::EM_ANALISE->value,
            'ind_aprovado' => false,
            'usu_inclusao' => $usuario?->id,
        ]);

        $this->reset(['des_necessidade', 'txt_descricao']);
        $this->mensagemSucesso = $rascunho
            ? 'Rascunho da necessidade salvo com sucesso!'
            : 'Necessidade registrada com sucesso!';
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
        @if ($produtoModel)
            <span class="font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 text-xs">
                {{ $produtoModel->nom_produto }}
            </span>
            <flux:icon name="chevron-right" class="w-3.5 h-3.5 text-slate-300" />
        @endif
        <span class="font-extrabold text-indigo-600">Nova Necessidade</span>
    </nav>

    <div class="page-header">
        <span class="eyebrow">NECESSIDADE</span>
        <h1>Nova Necessidade</h1>
        <p>Descreva a necessidade, ideia ou desafio para o produto selecionado.</p>
    </div>

    @if (! $produtoModel)
        <!-- Alerta de Produto Não Selecionado -->
        <x-card class="p-8 text-center space-y-4 max-w-lg mx-auto bg-amber-50/80 border border-amber-200 rounded-2xl shadow-sm my-6">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto font-bold text-xl">
                !
            </div>
            <div class="space-y-2">
                <h2 class="text-lg font-bold text-amber-950">Nenhum produto selecionado</h2>
                <p class="text-sm text-amber-900 leading-relaxed font-medium">
                    Selecione o produto para cadastrar uma nova necessidade.
                </p>
            </div>
            <div class="pt-3">
                <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm shadow-sm transition-colors w-full sm:w-auto" wire:navigate>
                    <flux:icon name="arrow-left" class="w-4 h-4" />
                    <span>Voltar&nbsp;</span>
                </a>
            </div>
        </x-card>
    @else
        @if ($mensagemSucesso)
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-sm" role="alert">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ $mensagemSucesso }}</span>
                </div>
                <button type="button" x-on:click="$wire.mensagemSucesso = ''" class="text-emerald-700 hover:text-emerald-950 font-bold p-1">&times;</button>
            </div>
        @endif

        <form wire:submit.prevent="salvar(false)" class="space-y-6" novalidate>
            <x-card class="space-y-5">
                <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Identificação da Necessidade</h2>

                <!-- Produto Fixo Vinculado (Bloqueado para alteração) -->
                <div class="flex items-center justify-between p-3.5 bg-indigo-50/70 border border-indigo-100 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <flux:icon name="cube" class="w-4 h-4 text-indigo-600 shrink-0" />
                        <span class="text-xs font-bold text-slate-600">Produto Vinculado:</span>
                        <strong class="text-sm font-extrabold text-indigo-950">{{ $produtoModel->nom_produto }}</strong>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-700 bg-indigo-100 px-2.5 py-0.5 rounded-full border border-indigo-200">
                        Fixado da Home
                    </span>
                </div>

                <div>
                    <flux:input wire:model="des_necessidade" label="Título da necessidade *" placeholder="Ex.: Exportação de relatório de infrações em lote" />
                    @error('des_necessidade') <span class="text-xs text-red-600 font-semibold mt-1 inline-block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <flux:textarea wire:model="txt_descricao" label="Descrição detalhada *" placeholder="Descreva o problema de negócio, os impactados e o objetivo esperado" rows="3" />
                    @error('txt_descricao') <span class="text-xs text-red-600 font-semibold mt-1 inline-block">{{ $message }}</span> @enderror
                </div>
            </x-card>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <flux:button type="submit" variant="primary" class="w-full sm:w-auto font-bold py-3">Enviar Necessidade</flux:button>
                <flux:button type="button" wire:click="salvar(true)" variant="filled" class="w-full sm:w-auto font-semibold py-3">Salvar Rascunho</flux:button>
            </div>
        </form>
    @endif
</div>
