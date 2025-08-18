<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Main Statistics Cards -->
<div class="row mb-4">
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card inventory-card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon purple mb-2">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Total Produk</h6>
                        <h6 class="font-extrabold mb-0" id="total-products"><?= number_format($total_products) ?></h6>
                        <small class="text-muted">Produk aktif</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3 col-md-6">
        <div class="card inventory-card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon blue mb-2">
                            <i class="bi bi-collection-fill"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Kategori</h6>
                        <h6 class="font-extrabold mb-0"><?= number_format($total_categories) ?></h6>
                        <small class="text-muted">Kategori aktif</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3 col-md-6">
        <div class="card inventory-card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon green mb-2">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Nilai Inventory</h6>
                        <h6 class="font-extrabold mb-0"><?= format_currency($inventory_value) ?></h6>
                        <small class="text-muted">Total valuasi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3 col-md-6">
        <div class="card inventory-card <?= $low_stock_count > 0 ? 'low-stock alert-stock' : '' ?>">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon <?= $low_stock_count > 0 ? 'red' : 'green' ?> mb-2">
                            <i class="bi bi-<?= $low_stock_count > 0 ? 'exclamation-triangle-fill' : 'check-circle-fill' ?>"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Stok Rendah</h6>
                        <h6 class="font-extrabold mb-0 <?= $low_stock_count > 0 ? 'text-danger' : 'text-success' ?>">
                            <?= number_format($low_stock_count) ?>
                        </h6>
                        <small class="text-muted">Perlu perhatian</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-speedometer2"></i> Statistik Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 col-md-3">
                        <div class="quick-stat">
                            <h4 class="text-info"><?= number_format($quick_stats['today_movements']) ?></h4>
                            <p class="text-muted mb-0">Transaksi Hari Ini</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-stat">
                            <h4 class="text-success"><?= number_format($quick_stats['this_week_in']) ?></h4>
                            <p class="text-muted mb-0">Barang Masuk (7 hari)</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-stat">
                            <h4 class="text-danger"><?= number_format($quick_stats['this_week_out']) ?></h4>
                            <p class="text-muted mb-0">Barang Keluar (7 hari)</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="quick-stat">
                            <h4 class="text-warning"><?= number_format($out_of_stock_count) ?></h4>
                            <p class="text-muted mb-0">Stok Habis</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-lightning-fill"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="<?= base_url('/stock/in') ?>" class="btn btn-info w-100 btn-lg">
                            <i class="bi bi-arrow-down-circle"></i>
                            <div class="d-block">
                                <strong>Barang Masuk</strong>
                                <small class="d-block">Input stok baru</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= base_url('/stock/out') ?>" class="btn btn-warning w-100 btn-lg">
                            <i class="bi bi-arrow-up-circle"></i>
                            <div class="d-block">
                                <strong>Barang Keluar</strong>
                                <small class="d-block">Keluarkan stok</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Dashboard Content Row -->
<div class="row">
    <!-- Stock Movement Chart -->
    <div class="col-12 col-xl-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-graph-up"></i> Pergerakan Stok 6 Bulan Terakhir</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-gear"></i> Options
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportChart('movements')">
                                <i class="bi bi-download"></i> Export Chart</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/reports/movements') ?>">
                                <i class="bi bi-file-earmark-text"></i> Detail Report</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <canvas id="stockMovementChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Stock Status Pie Chart -->
    <div class="col-12 col-xl-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-pie-chart"></i> Status Stok</h5>
            </div>
            <div class="card-body">
                <canvas id="stockStatusChart"></canvas>
                <div class="mt-3">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="stock-legend">
                                <div class="legend-color bg-success"></div>
                                <small>Normal</small>
                                <strong class="d-block"><?= $chart_data['stock_status_pie']['data'][2] ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stock-legend">
                                <div class="legend-color bg-warning"></div>
                                <small>Rendah</small>
                                <strong class="d-block text-warning"><?= $chart_data['stock_status_pie']['data'][1] ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stock-legend">
                                <div class="legend-color bg-danger"></div>
                                <small>Habis</small>
                                <strong class="d-block text-danger"><?= $chart_data['stock_status_pie']['data'][0] ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Stock Movements -->
    <div class="col-12 col-xl-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-clock-history"></i> Aktivitas Terbaru</h5>
                <a href="<?= base_url('/stock/history') ?>" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_movements)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="25%">Waktu</th>
                                    <th width="35%">Produk</th>
                                    <th width="20%">Tipe</th>
                                    <th width="20%">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_movements as $movement): ?>
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                <?= time_ago($movement['created_at']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= esc($movement['product_name']) ?></strong>
                                                <small class="d-block text-muted"><?= $movement['product_sku'] ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <?= format_movement_badge($movement['type']) ?>
                                        </td>
                                        <td>
                                            <strong class="<?= $movement['type'] == 'IN' ? 'text-success' : 'text-danger' ?>">
                                                <?= $movement['type'] == 'IN' ? '+' : '-' ?><?= number_format($movement['quantity']) ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted">Belum ada aktivitas</p>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="col-12 col-xl-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-exclamation-triangle-fill text-warning"></i> Produk Stok Rendah</h5>
                <a href="<?= base_url('/stock/alerts') ?>" class="btn btn-sm btn-outline-warning">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($low_stock_products)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="40%">Produk</th>
                                    <th width="20%">Stok</th>
                                    <th width="20%">Min</th>
                                    <th width="20%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low_stock_products as $product): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <div class="avatar-content bg-warning text-white">
                                                        <i class="bi bi-box"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong><?= esc($product['name']) ?></strong>
                                                    <small class="d-block text-muted"><?= $product['category_name'] ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="<?= $product['current_stock'] == 0 ? 'text-danger' : 'text-warning' ?>">
                                                <?= number_format($product['current_stock']) ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="text-muted"><?= number_format($product['min_stock']) ?></span>
                                        </td>
                                        <td>
                                            <?= format_stock_badge($product['current_stock'], $product['min_stock']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong><?= count($low_stock_products) ?></strong> produk membutuhkan restok segera.
                            <a href="<?= base_url('/stock/in') ?>" class="alert-link">Tambah stok sekarang</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                        <h6 class="text-success mt-2">Semua Stok Normal</h6>
                        <p class="text-muted">Tidak ada produk dengan stok rendah</p>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<!-- Top Products by Value -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-trophy-fill text-warning"></i> Top Produk Berdasarkan Nilai</h5>
                <a href="<?= base_url('/reports/valuation') ?>" class="btn btn-sm btn-outline-primary">
                    Detail Report
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($top_products)): ?>
                    <div class="row">
                        <?php foreach ($top_products as $index => $product): ?>
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="rank-badge me-3">
                                                <span class="badge bg-<?= $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'dark') ?> fs-6">
                                                    #<?= $index + 1 ?>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?= esc($product['name']) ?></h6>
                                                <small class="text-muted"><?= $product['category_name'] ?></small>
                                                <div class="mt-2">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Stok:</small>
                                                            <strong class="d-block"><?= number_format($product['current_stock']) ?></strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Nilai:</small>
                                                            <strong class="d-block text-success">
                                                                <?= format_currency($product['total_value']) ?>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-graph-down fs-1 text-muted"></i>
                        <p class="text-muted">Belum ada data produk dengan nilai tinggi</p>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Stock Movement Chart
        const movementCtx = document.getElementById('stockMovementChart').getContext('2d');
        const stockMovementChart = new Chart(movementCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chart_data['monthly_movements']['labels']) ?>,
                datasets: [{
                    label: 'Barang Masuk',
                    data: <?= json_encode($chart_data['monthly_movements']['stock_in']) ?>,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Barang Keluar',
                    data: <?= json_encode($chart_data['monthly_movements']['stock_out']) ?>,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Stock Status Pie Chart
        const statusCtx = document.getElementById('stockStatusChart').getContext('2d');
        const stockStatusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($chart_data['stock_status_pie']['labels']) ?>,
                datasets: [{
                    data: <?= json_encode($chart_data['stock_status_pie']['data']) ?>,
                    backgroundColor: <?= json_encode($chart_data['stock_status_pie']['colors']) ?>,
                    borderWidth: 0,
                    cutout: '70%' // ✅ pindah ke sini
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });


        // Real-time updates (every 30 seconds)
        setInterval(function() {
            updateDashboardStats();
        }, 30000);

        function updateDashboardStats() {
            $.ajax({
                url: '<?= base_url('/api/dashboard/stats') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        $('#total-products').text(data.stats.total_products.toLocaleString());
                        // Update other stats as needed
                    }
                },
                error: function() {
                    console.log('Failed to update dashboard stats');
                }
            });
        }

        // Export chart function
        window.exportChart = function(type) {
            let chart, filename;

            if (type === 'movements') {
                chart = stockMovementChart;
                filename = 'stock-movements-chart.png';
            } else if (type === 'status') {
                chart = stockStatusChart;
                filename = 'stock-status-chart.png';
            }

            if (chart) {
                const url = chart.toBase64Image();
                const link = document.createElement('a');
                link.download = filename;
                link.href = url;
                link.click();
            }
        };

        // Auto-refresh low stock alerts
        if (<?= $low_stock_count ?> > 0) {
            // Show notification every 5 minutes for low stock
            setInterval(function() {
                if (Notification.permission === 'granted') {
                    new Notification('Inventory Alert', {
                        body: '<?= $low_stock_count ?> produk memiliki stok rendah',
                        icon: '<?= base_url("assets/static/images/logo/favicon.png") ?>'
                    });
                }
            }, 300000); // 5 minutes
        }

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Dashboard Custom Styles */
    .quick-stat h4 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .btn-lg .d-block {
        line-height: 1.2;
    }

    .btn-lg strong {
        font-size: 1rem;
    }

    .btn-lg small {
        font-size: 0.8rem;
        opacity: 0.8;
    }

    .stock-legend {
        text-align: center;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: inline-block;
        margin-bottom: 8px;
    }

    .avatar-content {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
    }

    .rank-badge .badge {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1rem;
        font-weight: bold;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .quick-stat h4 {
            font-size: 1.5rem;
        }

        .btn-lg {
            padding: 0.75rem;
        }

        .btn-lg strong {
            font-size: 0.9rem;
        }

        .btn-lg small {
            font-size: 0.75rem;
        }
    }

    /* Animation for cards */
    .card {
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    /* Loading states */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
            
        }
    }
</style>
<?= $this->endSection() ?>