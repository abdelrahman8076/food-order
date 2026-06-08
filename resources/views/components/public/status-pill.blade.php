@props(['variant' => 'info'])

<span {{ $attributes->merge(['class' => "public-status-pill public-status-pill--{$variant}"]) }}>
    {{ $slot }}
</span>
