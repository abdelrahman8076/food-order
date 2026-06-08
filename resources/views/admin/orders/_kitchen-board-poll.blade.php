@push('scripts')
<script>
(function() {
    const boardUrl = @json(route('admin.orders.board'));
    let pollTimer = null;

    function refreshBoard() {
        fetch(boardUrl, { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                const container = document.getElementById('kitchenBoardColumns');
                if (container && data.html) {
                    container.innerHTML = data.html;
                }
            })
            .catch(function() {});
    }

    if (document.getElementById('kitchenBoardColumns')) {
        pollTimer = setInterval(refreshBoard, 15000);
    }
})();
</script>
@endpush
