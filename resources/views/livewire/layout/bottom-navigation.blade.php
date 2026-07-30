<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-lg sm:hidden">
    <nav class="flex items-center justify-around h-16 px-1 max-w-md mx-auto" aria-label="Navegação móvel">
        <!-- Mural / Home -->
        <a href="{{ route('welcome') }}" 
           wire:navigate 
           class="flex flex-col items-center justify-center min-w-[50px] min-h-[48px] px-1.5 rounded-xl transition-colors {{ request()->routeIs('welcome') ? 'text-indigo-600 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
            <flux:icon name="home" class="w-5 h-5 mb-0.5 shrink-0" />
            <span class="text-[10px] leading-tight truncate">Home</span>
        </a>

        <!-- Nova Necessidade (+) -->
        <a href="{{ route('necessidade.nova') }}" 
           wire:navigate 
           class="flex flex-col items-center justify-center min-w-[50px] min-h-[48px] px-1.5 rounded-xl transition-colors {{ request()->routeIs('necessidade.nova') ? 'text-indigo-600 font-extrabold' : 'text-indigo-700 hover:text-indigo-900' }}"
           title="Registrar Nova Necessidade">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-xs -mt-2 border-2 border-white">
                <flux:icon name="plus" class="w-5 h-5" />
            </div>
            <span class="text-[10px] font-bold leading-tight truncate mt-0.5">Necessidade</span>
        </a>

        <!-- Painel / Gestor -->
        <a href="{{ route('gestor.dashboard') }}" 
           wire:navigate 
           class="flex flex-col items-center justify-center min-w-[50px] min-h-[48px] px-1.5 rounded-xl transition-colors {{ request()->routeIs('gestor.dashboard') ? 'text-indigo-600 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
            <flux:icon name="queue-list" class="w-5 h-5 mb-0.5 shrink-0" />
            <span class="text-[10px] leading-tight truncate">Painel</span>
        </a>

        <!-- Perfil -->
        <a href="{{ route('profile') }}" 
           wire:navigate 
           class="flex flex-col items-center justify-center min-w-[50px] min-h-[48px] px-1.5 rounded-xl transition-colors {{ request()->routeIs('profile') ? 'text-indigo-600 font-extrabold' : 'text-slate-500 hover:text-slate-800' }}">
            <flux:icon name="user" class="w-5 h-5 mb-0.5 shrink-0" />
            <span class="text-[10px] leading-tight truncate">Perfil</span>
        </a>

        <!-- Sair -->
        <button type="button"
                wire:click="logout" 
                class="flex flex-col items-center justify-center min-w-[50px] min-h-[48px] px-1.5 rounded-xl text-slate-500 hover:text-red-600 transition-colors">
            <flux:icon name="arrow-right-start-on-rectangle" class="w-5 h-5 mb-0.5 shrink-0" />
            <span class="text-[10px] leading-tight truncate">Sair</span>
        </button>
    </nav>
</div>
