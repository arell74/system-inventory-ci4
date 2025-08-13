<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Filters & Search -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-funnel"></i> Filter & Pencarian</h5>
            </div>
            <div class="card-body">
                <form method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="search" class="form-label">Cari Produk</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= $search ?>" placeholder="Nama, SKU, atau deskripsi...">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" 
                                            <?= $categoryFilter == $category['id'] ? 'selected' : '' ?>>
                                        <?= esc($category['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="stock_status" class="form-label">Status Stok</label>
                            <select class="form-select" id="stock_status" name="stock_status">
                                <option value="">Semua Status</option>
                                <option value="normal" <?= $stockFilter == 'normal' ? 'selected' : '' ?>>
                                    Normal
                                </option>
                                <option value="low_stock" <?= $stockFilter == 'low_stock' ? 'selected' : '' ?>>
                                    Stok Rendah
                                </option>
                                <option value="out_of_stock" <?= $stockFilter == 'out_of_stock' ? 'selected' : '' ?>>
                                    Stok Habis
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <a href="<?= base_url('/products') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4>Daftar Produk</h4>
                    <small class="text-muted">Total: <?= number_format($totalProducts) ?> produk</small>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('/products/create') ?>" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah Produk
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/products/export/excel') ?>">
                                    <i class="bi bi-file-earmark-excel"></i> Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/products/export/pdf') ?>">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($products)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover datatable" id="productsTable">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Produk</th>
                                    <th width="15%">Kategori</th>
                                    <th width="10%">SKU</th>
                                    <th width="10%">Stok</th>
                                    <th width="12%">Harga</th>
                                    <th width="8%">Status</th>
                                    <th width="15%" class="no-sort">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $index => $product): ?>
                                <tr class="<?= $product['stock_status'] == 'out_of_stock' ? 'table-danger' : 
                                             ($product['stock_status'] == 'low_stock' ? 'table-warning' : '') ?>">
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <div class="avatar-content bg-primary text-white">
                                                    <i class="bi bi-box"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">
                                                    <a href="<?= base_url('/products/show/' . $product['id']) ?>" 
                                                       class="text-decoration-none">
                                                        <?= esc($product['name']) ?>
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    <?= $product['unit'] ?> | 
                                                    Dibuat: <?= date('d M Y', strtotime($product['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= esc($product['category_name']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <code><?= $product['sku'] ?></code>
                                    </td>
                                    <td>
                                        <div class="stock-info">
                                            <strong class="<?= $product['stock_status'] == 'out_of_stock' ? 'text-danger' : 
                                                             ($product['stock_status'] == 'low_stock' ? 'text-warning' : 'text-success') ?>">
                                                <?= number_format($product['current_stock']) ?>
                                            </strong>
                                            <small class="d-block text-muted">
                                                Min: <?= number_format($product['min_stock']) ?>
                                            </small>
                                            <?php if ($product['current_stock'] <= $product['min_stock']): ?>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-<?= $product['stock_status'] == 'out_of_stock' ? 'danger' : 'warning' ?>" 
                                                         style="width: <?= $product['current_stock'] > 0 ? ($product['current_stock'] / $product['min_stock'] * 100) : 0 ?>%">
                                                    </div>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= format_currency($product['price']) ?></strong>
                                            <?php if ($product['cost_price'] > 0): ?>
                                                <small class="d-block text-muted">
                                                    HPP: <?= format_currency($product['cost_price']) ?>
                                                </small>
                                                <small class="d-block text-success">
                                                    Margin: <?= number_format((($product['price'] - $product['cost_price']) / $product['price']) * 100, 1) ?>%
                                                </small>
                                            <?php endif ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= format_stock_badge($product['current_stock'], $product['min_stock']) ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('/products/show/' . $product['id']) ?>" 
                                               class="btn btn-sm btn-info" 
                                               data-bs-toggle="tooltip" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= base_url('/products/edit/' . $product['id']) ?>" 
                                               class="btn btn-sm btn-warning" 
                                               data-bs-toggle="tooltip" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger btn-delete" 
                                                    data-id="<?= $product['id'] ?>"
                                                    data-name="<?= esc($product['name']) ?>"
                                                    data-stock="<?= $product['current_stock'] ?>"
                                                    data-bs-toggle="tooltip" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-box fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Tidak ada produk ditemukan</h5>
                        <p class="text-muted">Silakan ubah filter atau tambahkan produk baru.</p>
                        <a href="<?= base_url('/products/create') ?>" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Tambah Produk Pertama
                        </a>
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
    <li class="breadcrumb-item active" aria-current="page">Produk</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#productsTable').DataTable({
        responsive: true,
        pageLength: 25,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        columnDefs: [
            { orderable: false, targets: 'no-sort' }
        ],
        order: [[1, 'asc']] // Sort by product name
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Delete product handler
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const stock = $(this).data('stock');
        
        showConfirm(
            'Konfirmasi Hapus Produk',
            `Yakin ingin menghapus produk "${name}"? Aksi ini tidak dapat dibatalkan.`,
            function() {
                deleteProduct(id, name);
            }
        );
    });

    function deleteProduct(id, name) {
        showLoading('Menghapus produk...');
        
        $.ajax({
            url: `<?= base_url('/products/delete/') ?>${id}`,
            type: 'DELETE',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                Swal.close();
                if (response.status) {
                    showSuccess('Berhasil!', response.message, function() {
                        location.reload();
                    });
                } else {
                    showError('Gagal!', response.message);
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                let message = 'Terjadi kesalahan saat menghapus produk';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showError('Error!', message);
            }
        });
    }

    // Auto-submit form on filter change
    $('#category, #stock_status').on('change', function() {
        $('#filterForm').submit();
    });

    // Search with delay
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            $('#filterForm').submit();
        }, 500);
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.stock-info {
    min-width: 80px;
}

.progress {
    border-radius: 2px;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

.table-danger {
    background-color: rgba(220, 53, 69, 0.1);
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

code {
    font-size: 0.9rem;
    color: #6f42c1;
    background-color: rgba(111, 66, 193, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 2px;
        margin-right: 0;
    }
}
</style>
<?= $this->endSection() ?>