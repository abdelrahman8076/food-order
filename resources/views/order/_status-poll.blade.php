@push('scripts')
<script>
(function() {
    const orderId = {{ $orderId }};
    let currentStatus = @json($initialStatus);
    const trackingToken = @json($trackingToken ?? null);
    const terminalStatuses = ['delivered', 'cancelled'];
    let pollTimer = null;

    function statusUrl() {
        let url = '/order/' + orderId + '/status';
        if (trackingToken) {
            url += '?token=' + encodeURIComponent(trackingToken);
        }
        return url;
    }

    function formatTimestamp(iso) {
        if (!iso) return '';
        const d = new Date(iso);
        return d.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        });
    }

    function renderDotContent(state) {
        if (state === 'completed') {
            return '<i class="bi bi-check-lg"></i>';
        }
        if (state === 'current') {
            return '<span class="timeline-pulse"></span>';
        }
        return '';
    }

    function renderTimelineStep(step) {
        const timeHtml = step.timestamp
            ? '<div class="timeline-time">' + formatTimestamp(step.timestamp) + '</div>'
            : '';

        return '<div class="timeline-step timeline-step--' + step.state + '" data-status="' + step.key + '">' +
            '<div class="timeline-dot">' + renderDotContent(step.state) + '</div>' +
            '<div class="timeline-content">' +
            '<div class="timeline-label">' + step.label + '</div>' +
            timeHtml +
            '</div></div>';
    }

    function updateOrderStatus(data) {
        currentStatus = data.status;

        const badge = document.getElementById('statusBadge');
        if (badge) {
            badge.textContent = data.status_label;
        }

        const timeline = document.getElementById('statusTimeline');
        if (timeline && Array.isArray(data.timeline)) {
            timeline.classList.toggle('status-timeline--cancelled', data.status === 'cancelled');
            timeline.innerHTML = data.timeline.map(renderTimelineStep).join('');
        }
    }

    function pollStatus() {
        fetch(statusUrl(), { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                updateOrderStatus(data);

                if (terminalStatuses.includes(data.status) && pollTimer) {
                    clearInterval(pollTimer);
                    pollTimer = null;
                }
            })
            .catch(function() {});
    }

    if (!terminalStatuses.includes(currentStatus)) {
        pollTimer = setInterval(pollStatus, 15000);
    }
})();
</script>
@endpush
