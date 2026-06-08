<div id="notifyPermissionBanner" class="alert alert-warning alert-dismissible fade show mb-0 rounded-0 d-none" role="alert" style="border-bottom: 1px solid var(--admin-border);">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <span><i class="bi bi-bell me-1"></i> Enable desktop alerts to get new order notifications when this tab is in the background.</span>
        <button type="button" class="btn btn-sm btn-admin-primary" id="notifyPermissionEnable">Enable</button>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" id="newOrderToastContainer" style="z-index: 1080;"></div>

<style>
    .new-order-toast {
        background: var(--admin-card);
        border: 1px solid var(--admin-orange);
        color: #fff;
        min-width: 300px;
    }
    .new-order-toast .toast-header {
        background: rgba(255, 126, 103, 0.15);
        border-bottom: 1px solid var(--admin-border);
        color: #fff;
    }
    .new-order-toast .btn-close {
        filter: invert(1);
    }
    #pendingOrdersBadge {
        background: var(--admin-orange);
        font-size: 0.65rem;
        vertical-align: top;
    }
</style>

<script>
(function() {
    const notifyUrl = @json(route('admin.orders.notifications'));
    const boardUrl = @json(route('admin.orders.board'));
    const soundUrl = @json(asset('sounds/new-order.wav'));
    const storageKey = 'adminLastSeenOrderId';
    const pollIntervalMs = 10000;

    const badge = document.getElementById('pendingOrdersBadge');
    const toastContainer = document.getElementById('newOrderToastContainer');
    const permissionBanner = document.getElementById('notifyPermissionBanner');
    const permissionEnableBtn = document.getElementById('notifyPermissionEnable');
    let pollTimer = null;
    let audioContext = null;
    let audioUnlocked = false;

    function getAudioContext() {
        if (!audioContext) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        return audioContext;
    }

    function unlockAudio() {
        if (audioUnlocked) return;
        try {
            const ctx = getAudioContext();
            if (ctx.state === 'suspended') {
                ctx.resume();
            }
            audioUnlocked = true;
        } catch (e) {}
    }

    function playWebAudioBeeps() {
        try {
            const ctx = getAudioContext();
            const start = ctx.currentTime;
            const beepDuration = 0.2;
            const gap = 0.1;
            const frequency = 880;

            for (let i = 0; i < 3; i++) {
                const t = start + i * (beepDuration + gap);
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = frequency;
                osc.type = 'square';
                gain.gain.setValueAtTime(0.001, t);
                gain.gain.exponentialRampToValueAtTime(0.45, t + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.001, t + beepDuration);
                osc.start(t);
                osc.stop(t + beepDuration);
            }
        } catch (e) {}
    }

    function playWavOnce() {
        return new Promise(function(resolve) {
            const audio = new Audio(soundUrl);
            audio.volume = 1.0;
            audio.onended = resolve;
            audio.onerror = resolve;
            audio.play().then(resolve).catch(resolve);
        });
    }

    function playNotificationSound() {
        unlockAudio();
        playWavOnce().then(function() {
            return new Promise(function(r) { setTimeout(r, 200); });
        }).then(playWavOnce).catch(function() {
            playWebAudioBeeps();
            setTimeout(playWebAudioBeeps, 1200);
        });
    }

    function updatePermissionBanner() {
        if (!permissionBanner || !('Notification' in window)) return;
        if (Notification.permission === 'default') {
            permissionBanner.classList.remove('d-none');
        } else {
            permissionBanner.classList.add('d-none');
        }
    }

    function requestNotificationPermission() {
        if (!('Notification' in window)) return;
        Notification.requestPermission().then(updatePermissionBanner);
    }

    if (permissionEnableBtn) {
        permissionEnableBtn.addEventListener('click', function() {
            unlockAudio();
            requestNotificationPermission();
        });
    }

    if (permissionBanner) {
        const navbar = document.querySelector('.navbar-admin');
        if (navbar) {
            navbar.insertAdjacentElement('afterend', permissionBanner);
        }
    }

    updatePermissionBanner();

    document.addEventListener('click', unlockAudio, { once: false });
    document.addEventListener('keydown', unlockAudio, { once: false });

    function updateBadge(count) {
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : String(count);
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }

    function showDesktopNotification(order) {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;

        const notification = new Notification('New Order — ' + order.order_number, {
            body: order.customer_name + ' — $' + order.total,
            tag: 'order-' + order.id,
            silent: false,
            requireInteraction: true,
        });

        notification.onclick = function() {
            window.focus();
            window.location.href = order.url;
            notification.close();
        };
    }

    function notifyNewOrder(order) {
        if (document.hidden) {
            showDesktopNotification(order);
        } else {
            showOrderToast(order);
        }
    }

    function showOrderToast(order) {
        const toastEl = document.createElement('div');
        toastEl.className = 'toast new-order-toast';
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        toastEl.innerHTML =
            '<div class="toast-header">' +
                '<i class="bi bi-bell-fill me-2" style="color: var(--admin-orange);"></i>' +
                '<strong class="me-auto">New Order</strong>' +
                '<small>just now</small>' +
                '<button type="button" class="btn-close" data-bs-dismiss="toast"></button>' +
            '</div>' +
            '<div class="toast-body">' +
                '<div class="fw-semibold">' + order.order_number + '</div>' +
                '<div class="small text-white-50">' + order.customer_name + '</div>' +
                '<div class="mt-2 d-flex justify-content-between align-items-center">' +
                    '<span class="fw-bold">$' + order.total + '</span>' +
                    '<a href="' + order.url + '" class="btn btn-sm btn-admin-primary">View</a>' +
                '</div>' +
            '</div>';

        toastContainer.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl, { delay: 10000 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', function() {
            toastEl.remove();
        });
    }

    function refreshKitchenBoard() {
        const container = document.getElementById('kitchenBoardColumns');
        if (!container) return;

        fetch(boardUrl, { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.html) {
                    container.innerHTML = data.html;
                }
            })
            .catch(function() {});
    }

    function pollNotifications() {
        const stored = sessionStorage.getItem(storageKey);
        const isSeeding = stored === null;
        const sinceId = stored ? parseInt(stored, 10) : 0;

        fetch(notifyUrl + '?since_id=' + sinceId, { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                updateBadge(data.pending_count);

                if (isSeeding) {
                    sessionStorage.setItem(storageKey, String(data.latest_id));
                    return;
                }

                if (data.new_orders && data.new_orders.length > 0) {
                    data.new_orders.forEach(notifyNewOrder);
                    playNotificationSound();
                    refreshKitchenBoard();
                }

                sessionStorage.setItem(storageKey, String(data.latest_id));
            })
            .catch(function() {});
    }

    pollNotifications();
    pollTimer = setInterval(pollNotifications, pollIntervalMs);

    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            pollNotifications();
        }
    });
})();
</script>
