@props([
    'variant' => 'primary', // primary, teal, amber, danger, slate
])

@php
    $classes = match($variant) {
        'teal' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
        'danger' => 'bg-red-50 text-red-700 border-red-200',
        'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
        default => 'bg-indigo-50 text-indigo-700 border-indigo-200',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full border ' . $classes]) }}>
    {{ $slot }}
</span>
