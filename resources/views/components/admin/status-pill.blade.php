@props(['variant' => 'muted', 'dot' => false])

<span {{ $attributes->merge(['class' => "admin-status-pill admin-status-pill--{$variant}"]) }}>
    @if($dot)
        <span class="admin-status-pill__dot"></span>
    @endif
    {{ $slot }}
</span>
