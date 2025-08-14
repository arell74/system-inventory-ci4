<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Filter & Search -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-funnel"></i> Filter Riwayat</h5>
            </div>
            <div class="card-body">
                <form method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="product" class="form-label">Produk</label>
                            <select class="form-select" id="product" name="product">
                                <option value="">Semua Produk</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>" 
                                            <?= $filters['product'] == $product['id'] ? 'selected' : '' ?>>
                                        <?= esc($product['name']) ?> (<?= $product['sku'] ?>)
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="type" class="form-label">Tipe</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Semua Tipe</option>
                                <option value="IN" <?= $filters['type'] == 'IN' ? 'selected' : '' ?>>
                                    Masuk
                                </option>
                                <option value="OUT" <?= $filters['type'] == 'OUT' ? 'selected' : '' ?>>
                                    Keluar
                                </option>
                                <option value="ADJUSTMENT" <?= $filters['type'] == 'ADJUSTMENT' ? 'selected' : '' ?>>
                                    Penyesuaian
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="start_date" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?= $filters['start_date'] ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="end_date" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?= $filters['end_date'] ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="reference" class="form-label">Referensi</label>
                            <input type="text" class="form-control" id="reference" name="reference" 
                                   value="<?= $filters['reference'] ?>" placeholder="Nomor referensi">
                        </div>
                        <div class="col-md-1 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                                <a href="<?= base_url('/stock/history') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3 col-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="stats-icon green mb-3">
                    <i class="bi bi-arrow-down-circle"></i>
                </div>
                <h4 class="text-success"><?= number_format($stats['total_in']) ?></h4>
                <p class="text-muted mb-0">Transaksi Masuk</p>
                <small class="text-muted"><?= number_format($stats['in_quantity']) ?> items</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="stats-icon red mb-3">
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
                <h4 class="text-danger"><?= number_format($stats['total_out']) ?></h4>
                <p class="text-muted mb-0">Transaksi Keluar</p>
                <small class="text-muted"><?= number_format($stats['out_quantity']) ?> items</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="stats-icon blue mb-3">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <h4 class="text-info"><?= number_format($stats['total_adjustments']) ?></h4>
                <p class="text-muted mb-0">Penyesuaian</p>
                <small class="text-muted"><?= number_format($stats['adjustment_quantity']) ?> adjustments</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="stats-icon purple mb-3">
                    <i class="bi bi-list-check"></i>
                </div>
                <h4 class="text-primary"><?= number_format($stats['total_movements']) ?></h4>
                <p class="text-muted mb-0">Total Transaksi</p>
                <small class="text-muted">Semua aktivitas</small>
            </div>
        </div>
    </div>
</div>

<!-- History Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4>Riwayat Pergerakan Stok</h4>
                    <small class="text-muted">Total: <?= number_format(count($movements)) ?> transaksi</small>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('/stock/in') ?>" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle"></i> Barang Masuk
                    </a>
                    <a href="<?= base_url('/stock/out') ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-minus-circle"></i> Barang Keluar
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/stock/history/export/excel?' . http_build_query($filters)) ?>">
                                    <i class="bi bi-file-earmark-excel"></i> Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/stock/history/export/pdf?' . http_build_query($filters)) ?>">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($movements)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover datatable" id="historyTable">
                            <thead>
                                <tr>
                                    <th width="12%">Tanggal/Waktu</th>
                                    <th width="20%">Produk</th>
                                    <th width="10%">Kategori</th>
                                    <th width="8%">Tipe</th>
                                    <th width="8%">Jumlah</th>
                                    <th width="8%">Stok Sebelum</th>
                                    <th width="8%">Stok Sesudah</th>
                                    <th width="12%">Referensi</th>
                                    <th width="14%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movements as $movement): ?>
                                <tr class="<?= $movement['type'] == 'OUT' ? 'table-warning' : 
                                             ($movement['type'] == 'ADJUSTMENT' ? 'table-info' : '') ?>">
                                    <td>
                                        <div>
                                            <strong><?= date('d M Y', strtotime($movement['created_at'])) ?></strong>
                                            <small class="d-block text-muted"><?= date('H:i', strtotime($movement['created_at'])) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <div class="avatar-content bg-<?= $movement['type'] == 'IN' ? 'success' : 
                                                                                 ($movement['type'] == 'OUT' ? 'warning' : 'info') ?> text-white">
                                                    <i class="bi bi-box"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?= esc($movement['product_name']) ?></h6>
                                                <small class="text-muted"><?= $movement['product_sku'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= esc($movement['category_name']) ?></span>
                                    </td>
                                    <td>
                                        <?= format_movement_badge($movement['type']) ?>
                                    </td>
                                    <td>
                                        <strong class="<?= $movement['type'] == 'IN' ? 'text-success' : 
                                                         ($movement['type'] == 'OUT' ? 'text-danger' : 'text-info') ?>">
                                            <?= $movement['type'] == 'IN' ? '+' : 
                                                ($movement['type'] == 'OUT' ? '-' : '±') ?>
                                            <?= number_format($movement['quantity']) ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?= number_format($movement['previous_stock']) ?></span>
                                    </td>
                                    <td>
                                        <strong><?= number_format($movement['current_stock']) ?></strong>
                                        <?php 
                                        $diff = $movement['current_stock'] - $movement['previous_stock'];
                                        if ($diff != 0):
                                        ?>
                                            <small class="d-block <?= $diff > 0 ? 'text-success' : 'text-danger' ?>">
                                                <?= $diff > 0 ? '+' : '' ?><?= $diff ?>
                                            </small>
                                        <?php endif ?>
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
                                        <?php if ($movement['created_by']): ?>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="bi bi-person"></i> <?= $movement['created_by'] ?>
                                                </small>
                                            </div>
                                        <?php endif ?>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-clock-history fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Tidak ada riwayat ditemukan</h5>
                        <p class="text-muted">Ubah filter atau mulai transaksi stok.</p>
                        <div class="btn-group">
                            <a href="<?= base_url('/stock/in') ?>" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Barang Masuk
                            </a>
                            <a href="<?= base_url('/stock/out') ?>" class="btn btn-warning">
                                <i class="bi bi-minus-circle"></i> Barang Keluar
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
    <li class="breadcrumb-item"><a href="<?= base_url('/stock/history') ?>">Stock</a></li>
    <li class="breadcrumb-item active" aria-current="page">History</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#historyTable').DataTable({
        responsive: true,
        pageLength: 50,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        order: [[0, 'desc']], // Sort by date descending
        columnDefs: [
            {
                targets: [3, 4, 5, 6], // Type, quantity, stock columns
                className: 'text-center'
            }
        ]
    });

    // Auto-submit form on filter change
    $('#product, #type').on('change', function() {
        $('#filterForm').submit();
    });

    // Date range validation
    $('#start_date, #end_date').on('change', function() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        
        if (startDate && endDate && startDate > endDate) {
            showError('Error', 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
            $(this).val('');
        }
    });

    // Real-time search with delay
    let searchTimeout;
    $('#reference').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            $('#filterForm').submit();
        }, 800);
    });

    // Quick date filters
    window.setDateFilter = function(days) {
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(endDate.getDate() - days);
        
        $('#start_date').val(startDate.toISOString().split('T')[0]);
        $('#end_date').val(endDate.toISOString().split('T')[0]);
        $('#filterForm').submit();
    };

    // Add quick filter buttons
    const quickFilters = `
        <div class="mt-2">
            <small class="text-muted">Quick Filter: </small>
            <button type="button" class="btn btn-outline-primary btn-xs" onclick="setDateFilter(7)">7 Hari</button>
            <button type="button" class="btn btn-outline-primary btn-xs" onclick="setDateFilter(30)">30 Hari</button>
            <button type="button" class="btn btn-outline-primary btn-xs" onclick="setDateFilter(90)">3 Bulan</button>
        </div>
    `;
    $('#end_date').parent().append(quickFilters);
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto;
}

.stats-icon.purple { background-color: rgba(102, 16, 242, 0.1); color: #6610f2; }
.stats-icon.blue { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
.stats-icon.green { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
.stats-icon.red { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }

.avatar-content {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

.table-info {
    background-color: rgba(13, 202, 240, 0.1);
}

code {
    font-size: 0.8rem;
    color: #6f42c1;
    background-color: rgba(111, 66, 193, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
}

.btn-xs {
    padding: 0.125rem 0.5rem;
    font-size: 0.75rem;
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .stats-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
<?= $this->endSection() ?>