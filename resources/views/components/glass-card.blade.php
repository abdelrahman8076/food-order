@props(['title' => null, 'headerClass' => ''])

<div {{ $attributes->merge(['class' => 'glass-card']) }}>
    @if($title)
        <div class="card-header {{ $headerClass }}">{{ $title }}</div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
