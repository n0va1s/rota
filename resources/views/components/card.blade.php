@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'modern-card ' . $class]) }}>
    {{ $slot }}
</div>
