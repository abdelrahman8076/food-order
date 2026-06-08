<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="admin-card card">
            <div class="card-header">{{ __('admin.analytics.chart_revenue_orders') }}</div>
            <div class="card-body">
                <canvas id="revenueOrdersChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-card card h-100">
            <div class="card-header">{{ __('admin.analytics.chart_top_qty') }}</div>
            <div class="card-body">
                <canvas id="topItemsQtyChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-card card h-100">
            <div class="card-header">{{ __('admin.analytics.chart_top_revenue') }}</div>
            <div class="card-body">
                <canvas id="topItemsRevenueChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-card card h-100">
            <div class="card-header">{{ __('admin.analytics.chart_delivery_area') }}</div>
            <div class="card-body">
                <canvas id="deliveryAreaChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-card card h-100">
            <div class="card-header">{{ __('admin.analytics.chart_category_revenue') }}</div>
            <div class="card-body">
                <canvas id="categoryRevenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
    const chartColors = ['#ff7e67', '#ffbe76', '#f0932b', '#eb4d4b', '#6ab04c', '#22a6b3', '#686de0', '#30336b', '#95afc0', '#dff9fb'];
    const chartText = 'rgba(255,255,255,0.75)';
    const chartGrid = 'rgba(255,255,255,0.08)';
    const i18n = {
        revenue: @json(__('admin.analytics.chart_revenue_label')),
        orders: @json(__('admin.analytics.orders')),
        quantity: @json(__('admin.analytics.chart_quantity')),
    };

    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                labels: { color: chartText }
            }
        },
        scales: {
            x: {
                ticks: { color: chartText },
                grid: { color: chartGrid }
            },
            y: {
                ticks: { color: chartText },
                grid: { color: chartGrid }
            }
        }
    };

    const revenueByDay = @json($revenueByDay);
    const topItemsByQuantity = @json($topItemsByQuantity);
    const topItemsByRevenue = @json($topItemsByRevenue);
    const ordersByDeliveryArea = @json($ordersByDeliveryArea);
    const revenueByCategory = @json($revenueByCategory);

    new Chart(document.getElementById('revenueOrdersChart'), {
        type: 'line',
        data: {
            labels: revenueByDay.map(row => row.date),
            datasets: [
                {
                    label: i18n.revenue,
                    data: revenueByDay.map(row => row.revenue),
                    borderColor: '#ff7e67',
                    backgroundColor: 'rgba(255,126,103,0.15)',
                    tension: 0.3,
                    yAxisID: 'y'
                },
                {
                    label: i18n.orders,
                    data: revenueByDay.map(row => row.orders),
                    borderColor: '#ffbe76',
                    backgroundColor: 'rgba(255,190,118,0.15)',
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            ...defaultOptions,
            scales: {
                x: defaultOptions.scales.x,
                y: {
                    ...defaultOptions.scales.y,
                    position: 'left',
                    title: { display: true, text: i18n.revenue, color: chartText }
                },
                y1: {
                    ...defaultOptions.scales.y,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: i18n.orders, color: chartText }
                }
            }
        }
    });

    function horizontalBarChart(canvasId, labels, data, label, color) {
        new Chart(document.getElementById(canvasId), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: color,
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: defaultOptions.scales.x,
                    y: {
                        ticks: { color: chartText },
                        grid: { color: chartGrid }
                    }
                }
            }
        });
    }

    horizontalBarChart(
        'topItemsQtyChart',
        topItemsByQuantity.map(row => row.name),
        topItemsByQuantity.map(row => row.quantity),
        i18n.quantity,
        '#ff7e67'
    );

    horizontalBarChart(
        'topItemsRevenueChart',
        topItemsByRevenue.map(row => row.name),
        topItemsByRevenue.map(row => row.revenue),
        i18n.revenue,
        '#ffbe76'
    );

    function donutChart(canvasId, labels, data) {
        new Chart(document.getElementById(canvasId), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: chartColors
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: chartText }
                    }
                }
            }
        });
    }

    donutChart(
        'deliveryAreaChart',
        ordersByDeliveryArea.map(row => row.area),
        ordersByDeliveryArea.map(row => row.orders)
    );

    donutChart(
        'categoryRevenueChart',
        revenueByCategory.map(row => row.category),
        revenueByCategory.map(row => row.revenue)
    );
})();
</script>
@endpush
