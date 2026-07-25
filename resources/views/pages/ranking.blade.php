<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

use function Livewire\Volt\computed;

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
        <p>Quem mais colabora com necessidades e tem mais ideias aprovadas pelos gestores.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <!-- Lista 1: Mais necessidades sugeridas -->
        <x-card class="space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-lg font-bold text-slate-900">Mais necessidades sugeridas</h2>
                <p class="text-xs text-slate-500">Total de necessidades registradas por pessoa.</p>
            </div>

            <ol class="divide-y divide-slate-100 list-none p-0 m-0 space-y-1">
                @forelse ($this->rankingSugeridas as $index => $user)
                    <li class="py-3 px-2 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $index === 0 ? 'bg-amber-100 text-amber-800' : ($index === 1 ? 'bg-slate-200 text-slate-700' : ($index === 2 ? 'bg-amber-50 text-amber-700' : 'bg-slate-50 text-slate-500')) }}">
                                {{ $index + 1 }}º
                            </span>
                            <span class="font-bold text-slate-800 text-sm sm:text-base">{{ $user->name }}</span>
                        </div>
                        <x-badge variant="teal">
                            {{ $user->total }} {{ Str::plural('necessidade', $user->total) }}
                        </x-badge>
                    </li>
                @empty
                    <li class="py-6 text-center text-xs text-slate-500">Nenhum registro encontrado.</li>
                @endforelse
            </ol>
        </x-card>

        <!-- Lista 2: Mais ideias aprovadas -->
        <x-card class="space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-lg font-bold text-slate-900">Mais ideias aprovadas</h2>
                <p class="text-xs text-slate-500">Necessidades com aprovação formal do gestor.</p>
            </div>

            <ol class="divide-y divide-slate-100 list-none p-0 m-0 space-y-1">
                @forelse ($this->rankingAprovadas as $index => $user)
                    <li class="py-3 px-2 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm {{ $index === 0 ? 'bg-amber-100 text-amber-800' : ($index === 1 ? 'bg-slate-200 text-slate-700' : ($index === 2 ? 'bg-amber-50 text-amber-700' : 'bg-slate-50 text-slate-500')) }}">
                                {{ $index + 1 }}º
                            </span>
                            <span class="font-bold text-slate-800 text-sm sm:text-base">{{ $user->name }}</span>
                        </div>
                        <x-badge variant="amber">
                            {{ $user->total }} {{ Str::plural('aprovada', $user->total) }}
                        </x-badge>
                    </li>
                @empty
                    <li class="py-6 text-center text-xs text-slate-500">Nenhuma ideia aprovada no momento.</li>
                @endforelse
            </ol>
        </x-card>
    </div>
</div>
