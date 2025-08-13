<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Product Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl me-4">
                                <div class="avatar-content bg-primary text-white">
                                    <i class="bi bi-box-seam fs-1"></i>
                                </div>
                            </div>
                            <div>
                                <h2 class="mb-1"><?= esc($product['name']) ?></h2>
                                <div class="mb-2">
                                    <span class="badge bg-info me-2"><?= esc($product['category_name']) ?></span>
                                    <code><?= $product['sku'] ?></code>
                                    <span class="text-muted ms-2">• <?= $product['unit'] ?></span>
                                </div>
                                <p class="text-muted mb-0"><?= $product['description'] ?: 'Tidak ada deskripsi' ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group mb-2" role="group">
                            <a href="<?= base_url('/products/edit/' . $product['id']) ?>" 
                               class="btn btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <a href="<?= base_url('/products') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div>
                            <?= format_stock_badge($product['current_stock'], $product['min_stock']) ?>
                            <?php if ($product['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Details -->
<div class="row">
    <!-- Stock & Pricing Info -->
    <div class="col-12 col-lg-8 mb-4">
        <div class="row">
            <!-- Stock Information -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5><i class="bi bi-box-seam"></i> Informasi Stok</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <h3 class="<?= $product['current_stock'] == 0 ? 'text-danger' : 
                                             ($product['current_stock'] <= $product['min_stock'] ? 'text-warning' : 'text-success') ?>">
                                    <?= number_format($product['current_stock']) ?>
                                </h3>
                                <small class="text-muted">Stok Saat Ini</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-muted"><?= number_format($product['min_stock']) ?></h3>
                                <small class="text-muted">Min. Stok</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-info"><?= number_format($stockStats['total_in'] - $stockStats['total_out']) ?></h3>
                                <small class="text-muted">Net Movement</small>
                            </div>
                        </div>
                        
                        <!-- Stock Progress -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Status Stok</small>
                                <small class="text-muted">
                                    <?= $product['min_stock'] > 0 ? 
                                        round(($product['current_stock'] / $product['min_stock']) * 100, 1) : 100 ?>%
                                </small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-<?= $product['current_stock'] == 0 ? 'danger' : 
                                                              ($product['current_stock'] <= $product['min_stock'] ? 'warning' : 'success') ?>" 
                                     style="width: <?= $product['min_stock'] > 0 ? 
                                                    min(($product['current_stock'] / $product['min_stock']) * 100, 100) : 100 ?>%">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stock Actions -->
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('/stock/in?product=' . $product['id']) ?>" 
                               class="btn btn-success btn-sm">
                                <i class="bi bi-arrow-down-circle"></i> Tambah Stok
                            </a>
                            <a href="<?= base_url('/stock/out?product=' . $product['id']) ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="bi bi-arrow-up-circle"></i> Kurangi Stok
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pricing Information -->
            <div class="col-12 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5><i class="bi bi-currency-dollar"></i> Informasi Harga</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted">Harga Jual</label>
                                <h3 class="text-primary mb-0"><?= format_currency($product['price']) ?></h3>
                                <small class="text-muted">per <?= $product['unit'] ?></small>
                            </div>
                            
                            <?php if ($product['cost_price'] > 0): ?>
                                <div class="col-12 mb-3">
                                    <label class="form-label text-muted">Harga Pokok (HPP)</label>
                                    <h4 class="text-secondary mb-0"><?= format_currency($product['cost_price']) ?></h4>
                                </div>
                                
                                <?php 
                                $margin = (($product['price'] - $product['cost_price']) / $product['price']) * 100;
                                $profit = $product['price'] - $product['cost_price'];
                                ?>
                                <div class="col-12">
                                    <div class="alert alert-<?= $margin < 10 ? 'danger' : ($margin < 20 ? 'warning' : 'success') ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Margin: <?= number_format($margin, 1) ?>%</strong>
                                                <div>Keuntungan: <?= format_currency($profit) ?></div>
                                            </div>
                                            <i class="bi bi-<?= $margin < 10 ? 'exclamation-triangle' : ($margin < 20 ? 'exclamation-circle' : 'check-circle') ?> fs-4"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>
                        
                        <!-- Inventory Value -->
                        <div class="border-top pt-3">
                            <div class="row text-center">
                                <div class="col-12">
                                    <label class="form-label text-muted">Total Nilai Inventory</label>
                                    <h4 class="text-success mb-0">
                                        <?= format_currency($product['current_stock'] * $product['price']) ?>
                                    </h4>
                                    <small class="text-muted"><?= number_format($product['current_stock']) ?> × <?= format_currency($product['price']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Metadata -->
    <div class="col-12 col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-info-circle"></i> Detail Produk</h5>
            </div>
            <div class="card-body">
                <div class="product-meta">
                    <div class="meta-item">
                        <label class="meta-label">ID Produk</label>
                        <div class="meta-value">#<?= str_pad($product['id'], 4, '0', STR_PAD_LEFT) ?></div>
                    </div>
                    
                    <div class="meta-item">
                        <label class="meta-label">SKU</label>
                        <div class="meta-value"><code><?= $product['sku'] ?></code></div>
                    </div>
                    
                    <div class="meta-item">
                        <label class="meta-label">Kategori</label>
                        <div class="meta-value">
                            <a href="<?= base_url('/products?category=' . $product['category_id']) ?>" 
                               class="text-decoration-none">
                                <?= esc($product['category_name']) ?>
                            </a>
                        </div>
                    </div>
                    
                    <div class="meta-item">
                        <label class="meta-label">Satuan</label>
                        <div class="meta-value"><?= ucfirst($product['unit']) ?></div>
                    </div>
                    
                    <div class="meta-item">
                        <label class="meta-label">Status</label>
                        <div class="meta-value">
                            <?php if ($product['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif ?>
                        </div>
                    </div>
                    
                    <div class="meta-item">
                        <label class="meta-label">Dibuat</label>
                        <div class="meta-value">
                            <?= date('d M Y', strtotime($product['created_at'])) ?>
                            <small class="text-muted d-block"><?= time_ago($product['created_at']) ?></small>
                        </div>
                    </div>
                    
                    <?php if ($product['updated_at'] && $product['updated_at'] != $product['created_at']): ?>
                        <div class="meta-item">
                            <label class="meta-label">Terakhir Update</label>
                            <div class="meta-value">
                                <?= date('d M Y', strtotime($product['updated_at'])) ?>
                                <small class="text-muted d-block"><?= time_ago($product['updated_at']) ?></small>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Movement Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-graph-up"></i> Statistik Pergerakan Stok</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="bi bi-arrow-down-circle"></i>
                            </div>
                            <h4 class="text-success"><?= number_format($stockStats['total_in']) ?></h4>
                            <p class="text-muted mb-0">Total Masuk</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-danger">
                                <i class="bi bi-arrow-up-circle"></i>
                            </div>
                            <h4 class="text-danger"><?= number_format($stockStats['total_out']) ?></h4>
                            <p class="text-muted mb-0">Total Keluar</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                            <h4 class="text-info"><?= count($stockMovements) ?></h4>
                            <p class="text-muted mb-0">Total Transaksi</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                            <h4 class="text-warning">
                                <?php 
                                $turnoverRate = $stockStats['total_out'] > 0 && $product['current_stock'] > 0 ? 
                                                round($stockStats['total_out'] / $product['current_stock'], 1) : 0;
                                echo $turnoverRate;
                                ?>
                            </h4>
                            <p class="text-muted mb-0">Turnover Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Stock Movements -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-clock-history"></i> Riwayat Pergerakan Stok (10 Terbaru)</h5>
                <a href="<?= base_url('/stock/history?product=' . $product['id']) ?>" 
                   class="btn btn-outline-primary btn-sm">
                    Lihat Semua Riwayat
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($stockMovements)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="15%">Tanggal</th>
                                    <th width="15%">Tipe</th>
                                    <th width="10%">Jumlah</th>
                                    <th width="15%">Stok Sebelum</th>
                                    <th width="15%">Stok Sesudah</th>
                                    <th width="15%">Ref. No</th>
                                    <th width="15%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stockMovements as $movement): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?= date('d M Y', strtotime($movement['created_at'])) ?></strong>
                                            <small class="d-block text-muted"><?= date('H:i', strtotime($movement['created_at'])) ?></small>
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
                                    <td>
                                        <span class="text-muted"><?= number_format($movement['previous_stock']) ?></span>
                                    </td>
                                    <td>
                                        <strong><?= number_format($movement['current_stock']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($movement['reference_no']): ?>
                                            <code><?= $movement['reference_no'] ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <small><?= $movement['notes'] ?: '-' ?></small>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-clock-history fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Belum Ada Pergerakan Stok</h5>
                        <p class="text-muted">Produk ini belum memiliki riwayat pergerakan stok.</p>
                        <div class="btn-group">
                            <a href="<?= base_url('/stock/in?product=' . $product['id']) ?>" 
                               class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Tambah Stok Pertama
                            </a>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('/products') ?>">Produk</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= esc($product['name']) ?></li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.avatar-content {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: bold;
}

.product-meta {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.meta-item {
    border-bottom: 1px solid rgba(0,0,0,0.1);
    padding-bottom: 0.75rem;
}

.meta-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.meta-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.25rem;
    display: block;
}

.meta-value {
    font-size: 1rem;
    color: #212529;
    font-weight: 500;
}

.stat-card {
    text-align: center;
    padding: 1rem;
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 24px;
    color: white;
}

.stat-card h4 {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.progress {
    height: 8px;
    border-radius: 4px;
}

code {
    font-size: 0.9rem;
    color: #6f42c1;
    background-color: rgba(111, 66, 193, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
}

/* Dark mode adjustments */
[data-bs-theme="dark"] .meta-item {
    border-bottom-color: rgba(255,255,255,0.1);
}

[data-bs-theme="dark"] .stat-card {
    border-color: rgba(255,255,255,0.1);
    background-color: rgba(255,255,255,0.02);
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .avatar-content {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }
    
    .stat-card {
        padding: 0.75rem;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
        margin-bottom: 0.75rem;
    }
    
    .stat-card h4 {
        font-size: 1.5rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}

/* Animation for stock alerts */
@keyframes stock-alert {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.stock-alert {
    animation: stock-alert 2s infinite;
}

/* Hover effects */
.card:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.btn-group .btn {
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Add stock alert animation for low stock products
    <?php if ($product['current_stock'] <= $product['min_stock'] && $product['current_stock'] > 0): ?>
        $('.progress-bar').addClass('stock-alert');
    <?php endif ?>
    
    // Real-time stock status updates (optional)
    function checkStockStatus() {
        // This could be enhanced to periodically check stock status
        // via AJAX and update the display in real-time
    }
    
    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Print functionality (if needed)
    window.printProduct = function() {
        window.print();
    };
    
    // Export functionality
    window.exportProduct = function(format) {
        const productId = <?= $product['id'] ?>;
        window.open(`<?= base_url('/products/export/') ?>${productId}?format=${format}`, '_blank');
    };
});
</script>
<?= $this->endSection() ?>