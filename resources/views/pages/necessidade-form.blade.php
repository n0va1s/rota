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
            <span class="font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 text-xs truncate max-w-[140px] sm:max-w-xs">
                {{ $produtoModel->nom_produto }}
            </span>
            <flux:icon name="chevron-right" class="w-3.5 h-3.5 text-slate-300 shrink-0" />
        @endif
        <span class="font-extrabold text-indigo-700">Nova Necessidade</span>
    </nav>

    <div class="page-header">
        <span class="eyebrow">NECESSIDADE</span>
        <h1>Nova Necessidade</h1>
        <p>Descreva a necessidade, ideia ou desafio para o produto selecionado.</p>
    </div>

    @if (! $produtoModel)
        <!-- Alerta de Produto Não Selecionado -->
        <x-card class="p-8 text-center space-y-4 max-w-lg mx-auto bg-amber-50/80 border border-amber-200 rounded-2xl shadow-sm my-6">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center mx-auto font-bold text-xl">
                !
            </div>
            <div class="space-y-2">
                <h2 class="text-lg font-bold text-amber-950">Nenhum produto selecionado</h2>
                <p class="text-sm text-amber-900 leading-relaxed font-medium">
                    Selecione o produto para cadastrar uma nova necessidade.
                </p>
            </div>
            <div class="pt-3">
                <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 min-h-[44px] rounded-xl bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-xs sm:text-sm shadow-sm transition-colors w-full sm:w-auto" wire:navigate>
                    <flux:icon name="arrow-left" class="w-4 h-4" />
                    <span>Voltar&nbsp;</span>
                </a>
            </div>
        </x-card>
    @else
        @if ($mensagemSucesso)
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 text-sm font-semibold flex items-center justify-between shadow-sm" role="alert">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <span>{{ $mensagemSucesso }}</span>
                </div>
                <button type="button" x-on:click="$wire.mensagemSucesso = ''" class="text-emerald-800 hover:text-emerald-950 font-bold p-1 me-2 min-h-[44px] min-w-[44px] inline-flex items-center justify-center">&times;</button>
            </div>
        @endif

        <form wire:submit.prevent="salvar(false)" 
              x-data="{
                  gravandoCampo: null,
                  recognition: null,
                  suportado: 'SpeechRecognition' in window || 'webkitSpeechRecognition' in window,
                  iniciarDitado(campoTarget) {
                      if (!this.suportado) {
                          alert('Reconhecimento de voz não é suportado neste navegador.');
                          return;
                      }

                      if (this.gravandoCampo === campoTarget) {
                          this.pararDitado();
                          return;
                      }

                      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                      this.recognition = new SpeechRecognition();
                      this.recognition.lang = 'pt-BR';
                      this.recognition.continuous = true;
                      this.recognition.interimResults = true;

                      this.gravandoCampo = campoTarget;

                      let textoInicial = $wire.get(campoTarget) || '';

                      this.recognition.onresult = (event) => {
                          let transcricao = '';
                          for (let i = event.resultIndex; i < event.results.length; i++) {
                              transcricao += event.results[i][0].transcript;
                          }
                          const textoFinal = (textoInicial ? textoInicial + ' ' : '') + transcricao;
                          $wire.set(campoTarget, textoFinal);
                      };

                      this.recognition.onerror = (event) => {
                          console.error('Erro de reconhecimento de voz:', event.error);
                          this.pararDitado();
                      };

                      this.recognition.onend = () => {
                          this.gravandoCampo = null;
                      };

                      this.recognition.start();
                  },
                  pararDitado() {
                      if (this.recognition) {
                          this.recognition.stop();
                      }
                      this.gravandoCampo = null;
                  }
              }"
              class="space-y-6" 
              novalidate>
            <x-card class="space-y-5">
                <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Identificação da Necessidade</h2>

                <!-- Produto Fixo Vinculado (Bloqueado para alteração - Responsivo) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 p-3.5 bg-indigo-50/70 border border-indigo-200 rounded-xl">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <flux:icon name="cube" class="w-4 h-4 text-indigo-700 shrink-0" />
                        <span class="text-xs font-bold text-slate-700 shrink-0">Produto Vinculado:</span>
                        <strong class="text-sm font-extrabold text-indigo-950 truncate break-word-safe">{{ $produtoModel->nom_produto }}</strong>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-800 bg-indigo-100 px-2.5 py-0.5 rounded-full border border-indigo-200 self-start sm:self-auto shrink-0">
                        Fixado da Home
                    </span>
                </div>

                <!-- Título da necessidade com Ditado por Voz -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-2">
                        <flux:label>Título da necessidade *</flux:label>
                        <button type="button" 
                                x-on:click="iniciarDitado('des_necessidade')"
                                :class="gravandoCampo === 'des_necessidade' ? 'bg-red-100 text-red-700 border-red-300 animate-pulse' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 border-slate-200'"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-xs font-bold transition-all min-h-[36px]"
                                title="Ditar título por voz">
                            <flux:icon name="microphone" class="w-4 h-4" />
                            <span x-text="gravandoCampo === 'des_necessidade' ? 'Ouvindo...' : 'Ditar por voz'"></span>
                        </button>
                    </div>
                    <flux:input wire:model="des_necessidade" placeholder="Ex.: Exportação de relatório de infrações em lote" />
                    @error('des_necessidade') <span class="text-xs text-red-700 font-bold mt-1 inline-block">{{ $message }}</span> @enderror
                </div>

                <!-- Descrição detalhada com Ditado por Voz -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-2">
                        <flux:label>Descrição detalhada *</flux:label>
                        <button type="button" 
                                x-on:click="iniciarDitado('txt_descricao')"
                                :class="gravandoCampo === 'txt_descricao' ? 'bg-red-100 text-red-700 border-red-300 animate-pulse' : 'bg-slate-100 text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 border-slate-200'"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-xs font-bold transition-all min-h-[36px]"
                                title="Ditar descrição por voz">
                            <flux:icon name="microphone" class="w-4 h-4" />
                            <span x-text="gravandoCampo === 'txt_descricao' ? 'Ouvindo...' : 'Ditar por voz'"></span>
                        </button>
                    </div>
                    <flux:textarea wire:model="txt_descricao" placeholder="Descreva o problema de negócio, os impactados e o objetivo esperado" rows="3" />
                    @error('txt_descricao') <span class="text-xs text-red-700 font-bold mt-1 inline-block">{{ $message }}</span> @enderror
                </div>
            </x-card>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <flux:button type="submit" variant="primary" class="w-full sm:w-auto font-bold min-h-[44px]">Enviar Necessidade</flux:button>
                <flux:button type="button" wire:click="salvar(true)" variant="filled" class="w-full sm:w-auto font-bold min-h-[44px]">Salvar Rascunho</flux:button>
            </div>
        </form>
    @endif
</div>
