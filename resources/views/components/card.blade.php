@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'modern-card bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm ' . $class]) }}>
    {{ $slot }}
</div>
