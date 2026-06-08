@push('scripts')
<script>
(function() {
    const pollOrders = @json($pollOrders);
    const terminalStatuses = ['delivered', 'cancelled'];
    let pollTimer = null;
    let activeOrderId = null;

    const orderMap = {};
    pollOrders.forEach(function(o) { orderMap[o.id] = o; });

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

    function updateOrderStatus(orderId, data) {
        if (orderMap[orderId]) {
            orderMap[orderId].status = data.status;
        }

        const badge = document.getElementById('statusBadge-' + orderId);
        if (badge) {
            badge.textContent = data.status_label;
        }

        const timeline = document.getElementById('statusTimeline-' + orderId);
        if (timeline && Array.isArray(data.timeline)) {
            timeline.classList.toggle('status-timeline--cancelled', data.status === 'cancelled');
            timeline.innerHTML = data.timeline.map(renderTimelineStep).join('');
        }
    }

    function statusUrl(orderId) {
        const meta = orderMap[orderId];
        if (!meta) return null;
        let url = '/order/' + orderId + '/status';
        if (meta.token) {
            url += '?token=' + encodeURIComponent(meta.token);
        }
        return url;
    }

    function stopPoll() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
        activeOrderId = null;
    }

    function pollStatus(orderId) {
        const url = statusUrl(orderId);
        if (!url) return;

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                updateOrderStatus(orderId, data);

                if (terminalStatuses.includes(data.status)) {
                    stopPoll();
                }
            })
            .catch(function() {});
    }

    window.trackAccordionPoll = {
        start: function(orderId) {
            stopPoll();

            const meta = orderMap[orderId];
            if (!meta || terminalStatuses.includes(meta.status)) {
                return;
            }

            activeOrderId = orderId;
            pollTimer = setInterval(function() { pollStatus(orderId); }, 15000);
        },
        stop: stopPoll,
    };
})();
</script>
@endpush
