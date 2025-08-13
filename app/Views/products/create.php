<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Produk Baru</h4>
                <p class="text-muted mb-0">Lengkapi informasi produk dengan detail</p>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/products/store') ?>" method="POST" id="productForm">
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
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="current_stock" class="form-label">Stok Awal</label>
                                <input type="number" 
                                       class="form-control <?= (session('errors.current_stock')) ? 'is-invalid' : '' ?>" 
                                       id="current_stock" 
                                       name="current_stock" 
                                       value="<?= old('current_stock', $product['current_stock']) ?>"
                                       min="0"
                                       placeholder="0">
                                <?php if (session('errors.current_stock')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.current_stock') ?>
                                    </div>
                                <?php endif ?>
                                <small class="text-muted">Jumlah stok saat ini</small>
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
                                    <button type="submit" class="btn btn-success" id="btnSubmit">
                                        <i class="bi bi-save"></i> Simpan Produk
                                    </button>
                                    <button type="submit" name="save_and_add" value="1" class="btn btn-outline-success">
                                        <i class="bi bi-plus-circle"></i> Simpan & Tambah Lagi
                                    </button>
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
        <!-- Preview Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-eye"></i> Preview Produk</h5>
            </div>
            <div class="card-body">
                <div class="product-preview">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-lg me-3">
                            <div class="avatar-content bg-primary text-white">
                                <i class="bi bi-box"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0" id="preview-name">Nama Produk</h6>
                            <small class="text-muted" id="preview-category">Kategori</small>
                            <div><code id="preview-sku">SKU</code></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Harga</small>
                            <div id="preview-price" class="fw-bold">Rp 0</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Stok</small>
                            <div id="preview-stock" class="fw-bold">0 pcs</div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">Status</small>
                        <div>
                            <span class="badge bg-success" id="preview-status">
                                <i class="bi bi-check-circle"></i> Aktif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tips Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-lightbulb"></i> Tips</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Tips Produk:</h6>
                    <ul class="mb-0">
                        <li>Gunakan nama yang jelas dan deskriptif</li>
                        <li>SKU sebaiknya mengandung kode kategori</li>
                        <li>Set minimum stok untuk alert otomatis</li>
                        <li>Hitung margin keuntungan dengan tepat</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-lightning"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('/categories/create') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-plus"></i> Tambah Kategori
                    </a>
                    <a href="<?= base_url('/products') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-list"></i> Lihat Semua Produk
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
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Real-time preview updates
    updatePreview();
    
    $('#name, #category_id, #sku, #price, #current_stock, #unit, #is_active').on('input change', function() {
        updatePreview();
    });

    function updatePreview() {
        const name = $('#name').val() || 'Nama Produk';
        const categoryText = $('#category_id option:selected').text() || 'Kategori';
        const sku = $('#sku').val() || 'SKU';
        const price = parseFloat($('#price').val()) || 0;
        const stock = parseInt($('#current_stock').val()) || 0;
        const unit = $('#unit').val() || 'pcs';
        const isActive = $('#is_active').is(':checked');
        
        $('#preview-name').text(name);
        $('#preview-category').text(categoryText);
        $('#preview-sku').text(sku);
        $('#preview-price').text(formatCurrency(price));
        $('#preview-stock').text(stock.toLocaleString() + ' ' + unit);
        
        if (isActive) {
            $('#preview-status').removeClass('bg-secondary').addClass('bg-success')
                .html('<i class="bi bi-check-circle"></i> Aktif');
        } else {
            $('#preview-status').removeClass('bg-success').addClass('bg-secondary')
                .html('<i class="bi bi-x-circle"></i> Nonaktif');
        }
    }

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
                    updatePreview();
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
                       .html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
    });

    // Auto focus
    $('#name').focus();
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

.product-preview {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    background-color: #f8f9fa;
}

.avatar-content {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
}

.form-control:focus, .form-select:focus {
    border-color: #435ebe;
    box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
}

code {
    font-size: 0.85rem;
    color: #6f42c1;
    background-color: rgba(111, 66, 193, 0.1);
    padding: 2px 6px;
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