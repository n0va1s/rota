<?php

use App\Models\Produto;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use function Livewire\Volt\computed;

$rankingGestoresDisponibilizadas = computed(function () {
    return User::select('users.name', DB::raw('count(necessidades.idt_necessidade) as total'))
        ->join('produtos', 'produtos.idt_gestor', '=', 'users.id')
        ->join('necessidades', 'necessidades.idt_produto', '=', 'produtos.idt_produto')
        ->where('necessidades.tip_status', 'disponibilizada')
        ->groupBy('users.id', 'users.name')
        ->orderByDesc('total')
        ->take(10)
        ->get();
});

$rankingSugeridas = computed(function () {
    return User::select('users.id', 'users.name', DB::raw('count(necessidades.idt_necessidade) as total'))
        ->join('necessidades', 'necessidades.idt_solicitante', '=', 'users.id')
        ->groupBy('users.id', 'users.name')
        ->orderByDesc('total')
        ->take(10)
        ->get();
});

$rankingAprovadas = computed(function () {
    return User::select('users.id', 'users.name', DB::raw('count(necessidades.idt_necessidade) as total'))
        ->join('necessidades', 'necessidades.idt_solicitante', '=', 'users.id')
        ->where('necessidades.ind_aprovado', true)
        ->groupBy('users.id', 'users.name')
        ->orderByDesc('total')
        ->take(10)
        ->get();
});
?>

<div class="space-y-6">
    <div class="page-header">
        <span class="eyebrow">Engajamento &amp; Reconhecimento</span>
        <h1>Ranking de contribuidores</h1>
        <p>Acompanhe os gestores com mais entregas concluídas e os usuários que mais sugerem e têm ideias aprovadas.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 items-start">
        <!-- Lista 1: Gestores com mais entregas (Disponibilizadas) -->
        <x-card class="space-y-4 border border-indigo-200/80 p-5 sm:p-6">
            <div class="border-b border-slate-100 pb-3.5 space-y-1">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <flux:icon name="trophy" class="w-4 h-4 text-indigo-600 shrink-0" />
                    <span>Mais entregas</span>
                </h2>
                <p class="text-xs text-slate-500 leading-relaxed">Gestores com mais necessidades disponibilizadas no produto.</p>
            </div>

            <ol class="divide-y divide-slate-100 list-none p-0 m-0 space-y-1">
                @forelse ($this->rankingGestoresDisponibilizadas as $index => $gestor)
                    <li class="py-2.5 px-2 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs {{ $index === 0 ? 'bg-amber-400 text-white shadow-xs' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600')) }}">
                                {{ $index + 1 }}º
                            </span>
                            <span class="font-bold text-slate-800 text-xs sm:text-sm">{{ $gestor->name }}</span>
                        </div>
                        <x-badge variant="primary" size="sm">
                            {{ $gestor->total }} {{ Str::plural('entregue', $gestor->total) }}
                        </x-badge>
                    </li>
                @empty
                    <li class="py-6 text-center text-xs text-slate-400 italic">Nenhum gestor com necessidade disponibilizada no momento.</li>
                @endforelse
            </ol>
        </x-card>

        <!-- Lista 2: Mais necessidades sugeridas -->
        <x-card class="space-y-4 p-5 sm:p-6">
            <div class="border-b border-slate-100 pb-3.5 space-y-1">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <flux:icon name="light-bulb" class="w-4 h-4 text-teal-600 shrink-0" />
                    <span>Mais necessidades sugeridas</span>
                </h2>
                <p class="text-xs text-slate-500 leading-relaxed">Total de necessidades registradas por pessoa.</p>
            </div>

            <ol class="divide-y divide-slate-100 list-none p-0 m-0 space-y-1">
                @forelse ($this->rankingSugeridas as $index => $user)
                    <li class="py-2.5 px-2 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs {{ $index === 0 ? 'bg-amber-400 text-white shadow-xs' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600')) }}">
                                {{ $index + 1 }}º
                            </span>
                            <span class="font-bold text-slate-800 text-xs sm:text-sm">{{ $user->name }}</span>
                        </div>
                        <x-badge variant="teal" size="sm">
                            {{ $user->total }} {{ Str::plural('necessidade', $user->total) }}
                        </x-badge>
                    </li>
                @empty
                    <li class="py-6 text-center text-xs text-slate-400 italic">Nenhum registro encontrado.</li>
                @endforelse
            </ol>
        </x-card>

        <!-- Lista 3: Mais ideias aprovadas -->
        <x-card class="space-y-4 p-5 sm:p-6">
            <div class="border-b border-slate-100 pb-3.5 space-y-1">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <flux:icon name="check-badge" class="w-4 h-4 text-amber-500 shrink-0" />
                    <span>Mais ideias aprovadas</span>
                </h2>
                <p class="text-xs text-slate-500 leading-relaxed">Necessidades com aprovação formal do gestor.</p>
            </div>

            <ol class="divide-y divide-slate-100 list-none p-0 m-0 space-y-1">
                @forelse ($this->rankingAprovadas as $index => $user)
                    <li class="py-2.5 px-2 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs {{ $index === 0 ? 'bg-amber-400 text-white shadow-xs' : ($index === 1 ? 'bg-slate-300 text-slate-800' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600')) }}">
                                {{ $index + 1 }}º
                            </span>
                            <span class="font-bold text-slate-800 text-xs sm:text-sm">{{ $user->name }}</span>
                        </div>
                        <x-badge variant="amber" size="sm">
                            {{ $user->total }} {{ Str::plural('aprovada', $user->total) }}
                        </x-badge>
                    </li>
                @empty
                    <li class="py-6 text-center text-xs text-slate-400 italic">Nenhuma ideia aprovada no momento.</li>
                @endforelse
            </ol>
        </x-card>
    </div>
</div>
