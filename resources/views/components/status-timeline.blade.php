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

@once
@push('styles')
<style>
    .status-timeline {
        display: flex;
        flex-direction: column;
        gap: 0;
        padding: 1rem 0;
    }

    .timeline-step {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        position: relative;
        padding-bottom: 1.5rem;
    }

    .timeline-step:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 32px;
        bottom: 0;
        width: 2px;
        background: var(--glass-border);
    }

    .timeline-step--completed:not(:last-child)::before {
        background: var(--primary-orange);
    }

    .timeline-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid var(--glass-border);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(10, 10, 10, 0.5);
        z-index: 1;
        font-size: 0.85rem;
    }

    .timeline-step--completed .timeline-dot {
        background: var(--primary-orange);
        border-color: var(--primary-orange);
        color: white;
    }

    .timeline-step--current .timeline-dot {
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 4px rgba(255, 126, 103, 0.25);
    }

    .timeline-pulse {
        width: 10px;
        height: 10px;
        background: var(--primary-orange);
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.8); }
    }

    .timeline-label {
        font-weight: 600;
        color: #fff;
    }

    .timeline-step--pending .timeline-label {
        color: rgba(255, 255, 255, 0.4);
    }

    .timeline-time {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.5);
        margin-top: 2px;
    }

    .status-timeline--cancelled .timeline-step--current .timeline-dot {
        background: #dc3545;
        border-color: #dc3545;
        box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.25);
    }

    @media (min-width: 768px) {
        .status-timeline {
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
        }

        .timeline-step {
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
            padding-bottom: 0;
        }

        .timeline-step:not(:last-child)::before {
            left: 50%;
            top: 16px;
            right: auto;
            width: auto;
            height: 2px;
            bottom: auto;
            margin-left: 16px;
            width: calc(100% - 32px);
        }

        .timeline-content { margin-top: 0.5rem; }
    }
</style>
@endpush
@endonce
