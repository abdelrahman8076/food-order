@props(['order', 'timeline' => null, 'timelineId' => 'statusTimeline'])

@php
    $timeline = $timeline ?? app(\App\Services\OrderService::class)->getTimeline($order);
    $isCancelled = $order->status === 'cancelled';
@endphp

<div class="status-timeline {{ $isCancelled ? 'status-timeline--cancelled' : '' }}" id="{{ $timelineId }}">
    @foreach($timeline as $step)
        <div class="timeline-step timeline-step--{{ $step['state'] }}" data-status="{{ $step['key'] }}">
            <div class="timeline-dot">
                @if($step['state'] === 'completed')
                    <i class="bi bi-check-lg"></i>
                @elseif($step['state'] === 'current')
                    <span class="timeline-pulse"></span>
                @endif
            </div>
            <div class="timeline-content">
                <div class="timeline-label">{{ $step['label'] }}</div>
                @if($step['timestamp'])
                    <div class="timeline-time">{{ $step['timestamp']->format('M d, g:i A') }}</div>
                @endif
            </div>
        </div>
    @endforeach
</div>
