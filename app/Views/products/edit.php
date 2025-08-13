<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4>Edit Produk</h4>
                <p class="text-muted mb-0">Edit produk: <strong><?= esc($product['name']) ?></strong></p>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/products/update/' . $product['id']) ?>" method="POST" id="productForm">
                    <?= csrf_field() ?>
                    
                    <!-- Basic Information -->
                    <div class="form-section mb-4">
                        <h5 class="section-title">
                            <i class="bi bi-info-circle"></i> Informasi Dasar
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= (session('errors.name')) ? 'is-invalid' : '' ?>" 
                                       id="name" 
                                       name="name" 
                                       value="<?= old('name', $product['name']) ?>"
                                       placeholder="Masukkan nama produk"
                                       required>
                                <?php if (session('errors.name')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.name') ?>
                                    </div>
                                <?php endif ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select <?= (session('errors.category_id')) ? 'is-invalid' : '' ?>" 
                                        id="category_id" 
                                        name="category_id" 
                                        required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" 
                                                <?= old('category_id', $product['category_id']) == $category['id'] ? 'selected' : '' ?>>
                                            <?= esc($category['name']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <?php if (session('errors.category_id')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.category_id') ?>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control <?= (session('errors.sku')) ? 'is-invalid' : '' ?>" 
                                           id="sku" 
                                           name="sku" 
                                           value="<?= old('sku', $product['sku']) ?>"
                                           placeholder="Stock Keeping Unit"
                                           required>
                                    <button class="btn btn-outline-primary" type="button" id="generateSKU">
                                        <i class="bi bi-magic"></i> Generate
                                    </button>
                                    <?php if (session('errors.sku')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.sku') ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                                <small class="text-muted">SKU harus unik untuk setiap produk</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="unit" class="form-label">Satuan</label>
                                <select class="form-select" id="unit" name="unit">
                                    <option value="pcs" <?= old('unit', $product['unit']) == 'pcs' ? 'selected' : '' ?>>Pieces</option>
                                    <option value="kg" <?= old('unit', $product['unit']) == 'kg' ? 'selected' : '' ?>>Kilogram</option>
                                    <option value="gram" <?= old('unit', $product['unit']) == 'gram' ? 'selected' : '' ?>>Gram</option>
                                    <option value="liter" <?= old('unit', $product['unit']) == 'liter' ? 'selected' : '' ?>>Liter</option>
                                    <option value="ml" <?= old('unit', $product['unit']) == 'ml' ? 'selected' : '' ?>>Mililiter</option>
                                    <option value="meter" <?= old('unit', $product['unit']) == 'meter' ? 'selected' : '' ?>>Meter</option>
                                    <option value="box" <?= old('unit', $product['unit']) == 'box' ? 'selected' : '' ?>>Box</option>
                                    <option value="pack" <?= old('unit', $product['unit']) == 'pack' ? 'selected' : '' ?>>Pack</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control <?= (session('errors.description')) ? 'is-invalid' : '' ?>" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Deskripsi produk (opsional)"><?= old('description', $product['description']) ?></textarea>
                                <?php if (session('errors.description')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.description') ?>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Information -->
                    <div class="form-section mb-4">
                        <h5 class="section-title">
                            <i class="bi bi-currency-dollar"></i> Informasi Harga
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cost_price" class="form-label">Harga Pokok (HPP)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control <?= (session('errors.cost_price')) ? 'is-invalid' : '' ?>" 
                                           id="cost_price" 
                                           name="cost_price" 
                                           value="<?= old('cost_price', $product['cost_price']) ?>"
                                           min="0"
                                           step="0.01"
                                           placeholder="0">
                                    <?php if (session('errors.cost_price')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.cost_price') ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                                <small class="text-muted">Harga beli/produksi</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control <?= (session('errors.price')) ? 'is-invalid' : '' ?>" 
                                           id="price" 
                                           name="price" 
                                           value="<?= old('price', $product['price']) ?>"
                                           min="0"
                                           step="0.01"
                                           placeholder="0">
                                    <?php if (session('errors.price')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.price') ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                                <small class="text-muted">Harga jual ke customer</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info" id="marginInfo" style="display: none;">
                                    <i class="bi bi-info-circle"></i>
                                    <span id="marginText"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Information -->
                    <div class="form-section mb-4">
                        <h5 class="section-title">
                            <i class="bi bi-box-seam"></i> Informasi Stok
                        </h5>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Perhatian:</strong> Stok saat ini tidak bisa diubah dari halaman ini. 
                            Gunakan <a href="<?= base_url('/stock/in') ?>">Barang Masuk</a> atau 
                            <a href="<?= base_url('/stock/out') ?>">Barang Keluar</a> untuk mengubah stok.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="current_stock_display" class="form-label">Stok Saat Ini</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="current_stock_display" 
                                       value="<?= number_format($product['current_stock']) ?> <?= $product['unit'] ?>"
                                       readonly>
                                <small class="text-muted">Read-only - gunakan menu stok untuk mengubah</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="min_stock" class="form-label">Minimum Stok</label>
                                <input type="number" 
                                       class="form-control <?= (session('errors.min_stock')) ? 'is-invalid' : '' ?>" 
                                       id="min_stock" 
                                       name="min_stock" 
                                       value="<?= old('min_stock', $product['min_stock']) ?>"
                                       min="0"
                                       placeholder="10">
                                <?php if (session('errors.min_stock')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.min_stock') ?>
                                    </div>
                                <?php endif ?>
                                <small class="text-muted">Batas minimum untuk alert</small>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="form-section mb-4">
                        <h5 class="section-title">
                            <i class="bi bi-gear"></i> Pengaturan
                        </h5>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           <?= old('is_active', $product['is_active']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">
                                        Produk Aktif
                                    </label>
                                </div>
                                <small class="text-muted">Produk aktif akan ditampilkan dalam sistem</small>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('/products') ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                                        <i class="bi bi-save"></i> Update Produk
                                    </button>
                                    <a href="<?= base_url('/products/show/' . $product['id']) ?>" 
                                       class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Info Panel -->
    <div class="col-12 col-lg-4">
        <!-- Product Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-info-circle"></i> Info Produk</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">ID Produk</label>
                        <p class="fw-bold"><?= $product['id'] ?></p>
                    </div>
                    
                    <div class="col-6 mb-3">
                        <label class="form-label text-muted">Dibuat</label>
                        <p class="mb-0"><?= date('d M Y', strtotime($product['created_at'])) ?></p>
                        <small class="text-muted"><?= date('H:i', strtotime($product['created_at'])) ?></small>
                    </div>
                    
                    <div class="col-6 mb-3">
                        <label class="form-label text-muted">Diupdate</label>
                        <?php if ($product['updated_at'] && $product['updated_at'] != $product['created_at']): ?>
                            <p class="mb-0"><?= date('d M Y', strtotime($product['updated_at'])) ?></p>
                            <small class="text-muted"><?= date('H:i', strtotime($product['updated_at'])) ?></small>
                        <?php else: ?>
                            <p class="mb-0 text-muted">-</p>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stock Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-box-seam"></i> Status Stok</h5>
            </div>
            <div class="card-body">
                <?php 
                $stockStatus = 'normal';
                if ($product['current_stock'] == 0) {
                    $stockStatus = 'out_of_stock';
                } elseif ($product['current_stock'] <= $product['min_stock']) {
                    $stockStatus = 'low_stock';
                }
                ?>
                
                <div class="text-center">
                    <?php if ($stockStatus == 'out_of_stock'): ?>
                        <i class="bi bi-x-circle-fill fs-1 text-danger"></i>
                        <h5 class="text-danger mt-2">Stok Habis</h5>
                        <p class="text-muted">Produk tidak tersedia</p>
                    <?php elseif ($stockStatus == 'low_stock'): ?>
                        <i class="bi bi-exclamation-triangle-fill fs-1 text-warning"></i>
                        <h5 class="text-warning mt-2">Stok Rendah</h5>
                        <p class="text-muted">Segera lakukan restok</p>
                    <?php else: ?>
                        <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                        <h5 class="text-success mt-2">Stok Normal</h5>
                        <p class="text-muted">Stok dalam kondisi baik</p>
                    <?php endif ?>
                </div>

                <div class="row text-center mt-3">
                    <div class="col-6">
                        <h4 class="text-primary"><?= number_format($product['current_stock']) ?></h4>
                        <small class="text-muted">Stok Saat Ini</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-muted"><?= number_format($product['min_stock']) ?></h4>
                        <small class="text-muted">Minimum Stok</small>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar bg-<?= $stockStatus == 'out_of_stock' ? 'danger' : 
                                                      ($stockStatus == 'low_stock' ? 'warning' : 'success') ?>" 
                             style="width: <?= $product['min_stock'] > 0 ? 
                                             min(($product['current_stock'] / $product['min_stock']) * 100, 100) : 100 ?>%">
                        </div>
                    </div>
                </div>

                <?php if ($stockStatus != 'normal'): ?>
                    <div class="mt-3 d-grid gap-2">
                        <a href="<?= base_url('/stock/in') ?>" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Tambah Stok
                        </a>
                    </div>
                <?php endif ?>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-lightning"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('/stock/in?product=' . $product['id']) ?>" class="btn btn-success btn-sm">
                        <i class="bi bi-arrow-down-circle"></i> Barang Masuk
                    </a>
                    <a href="<?= base_url('/stock/out?product=' . $product['id']) ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-arrow-up-circle"></i> Barang Keluar
                    </a>
                    <a href="<?= base_url('/stock/history?product=' . $product['id']) ?>" class="btn btn-info btn-sm">
                        <i class="bi bi-clock-history"></i> Riwayat Stok
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('/products') ?>">Produk</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Store original values for change detection
    const originalData = {
        name: "<?= $product['name'] ?>",
        category_id: "<?= $product['category_id'] ?>",
        sku: "<?= $product['sku'] ?>",
        description: "<?= $product['description'] ?>",
        price: parseFloat("<?= $product['price'] ?>"),
        cost_price: parseFloat("<?= $product['cost_price'] ?>"),
        min_stock: parseInt("<?= $product['min_stock'] ?>"),
        unit: "<?= $product['unit'] ?>",
        is_active: <?= $product['is_active'] ? 'true' : 'false' ?>
    };

    // Check for changes
    function checkChanges() {
        const currentData = {
            name: $('#name').val(),
            category_id: $('#category_id').val(),
            sku: $('#sku').val(),
            description: $('#description').val(),
            price: parseFloat($('#price').val()) || 0,
            cost_price: parseFloat($('#cost_price').val()) || 0,
            min_stock: parseInt($('#min_stock').val()) || 0,
            unit: $('#unit').val(),
            is_active: $('#is_active').is(':checked')
        };

        const hasChanges = JSON.stringify(originalData) !== JSON.stringify(currentData);
        
        if (hasChanges) {
            $('#btnSubmit').removeClass('btn-primary').addClass('btn-warning')
                          .html('<i class="bi bi-save"></i> Update Produk (Ada Perubahan)');
        } else {
            $('#btnSubmit').removeClass('btn-warning').addClass('btn-primary')
                          .html('<i class="bi bi-save"></i> Update Produk');
        }
    }

    // Monitor changes
    $('#name, #category_id, #sku, #description, #price, #cost_price, #min_stock, #unit, #is_active')
        .on('input change', checkChanges);

    // Calculate margin
    $('#cost_price, #price').on('input', function() {
        calculateMargin();
    });

    function calculateMargin() {
        const costPrice = parseFloat($('#cost_price').val()) || 0;
        const sellPrice = parseFloat($('#price').val()) || 0;
        
        if (costPrice > 0 && sellPrice > 0) {
            const margin = ((sellPrice - costPrice) / sellPrice) * 100;
            const profit = sellPrice - costPrice;
            
            let marginClass = 'alert-info';
            let marginIcon = 'bi-info-circle';
            
            if (margin < 10) {
                marginClass = 'alert-danger';
                marginIcon = 'bi-exclamation-triangle';
            } else if (margin < 20) {
                marginClass = 'alert-warning';
                marginIcon = 'bi-exclamation-circle';
            } else {
                marginClass = 'alert-success';
                marginIcon = 'bi-check-circle';
            }
            
            $('#marginInfo').removeClass().addClass(`alert ${marginClass}`).show();
            $('#marginText').html(`
                <i class="bi ${marginIcon}"></i>
                <strong>Margin: ${margin.toFixed(1)}%</strong> 
                (Keuntungan: ${formatCurrency(profit)})
            `);
        } else {
            $('#marginInfo').hide();
        }
    }

    // Generate SKU
    $('#generateSKU').on('click', function() {
        const categoryId = $('#category_id').val();
        const productName = $('#name').val();
        
        if (!categoryId || !productName) {
            showError('Error', 'Pilih kategori dan isi nama produk terlebih dahulu');
            return;
        }

        const $btn = $(this);
        const originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.ajax({
            url: '<?= base_url('/products/generate-sku') ?>',
            type: 'POST',
            data: {
                category_id: categoryId,
                product_name: productName,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#sku').val(response.sku);
                    checkChanges();
                    showAlert('SKU berhasil di-generate!', 'success');
                } else {
                    showError('Error', response.message);
                }
            },
            error: function() {
                showError('Error', 'Gagal generate SKU');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Form submission
    $('#productForm').on('submit', function() {
        $('#btnSubmit').prop('disabled', true)
                       .html('<span class="spinner-border spinner-border-sm me-2"></span>Mengupdate...');
    });

    // Initial calculations
    calculateMargin();
    checkChanges();
    
    // Auto focus on name field
    $('#name').focus().select();
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.form-section {
    border-left: 4px solid #435ebe;
    padding-left: 1rem;
    margin-bottom: 2rem;
}

.section-title {
    color: #435ebe;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid rgba(67, 94, 190, 0.1);
}

.form-control:focus, .form-select:focus {
    border-color: #435ebe;
    box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
}

.btn-warning {
    animation: pulse-warning 2s infinite;
}

@keyframes pulse-warning {
    0% { opacity: 1; }
    50% { opacity: 0.8; }
    100% { opacity: 1; }
}

.progress {
    height: 8px;
    border-radius: 4px;
}

@media (max-width: 768px) {
    .form-section {
        padding-left: 0.5rem;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 0.5rem;
    }
}
</style>
<?= $this->endSection() ?>  