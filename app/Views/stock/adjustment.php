<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Header with Warning -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1">
                            <i class="bi bi-tools text-warning"></i> 
                            Penyesuaian Stok
                        </h4>
                        <p class="text-muted mb-0">Koreksi stok untuk menyesuaikan dengan kondisi fisik</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Perhatian!</strong><br>
                            <small>Fitur ini mengubah stok sistem sesuai kondisi fisik</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Form -->
    <div class="col-12 col-lg-9">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-clipboard-check"></i> Form Penyesuaian Stok</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/stock/adjustment/store') ?>" method="POST" id="adjustmentForm">
                    <?= csrf_field() ?>
                    
                    <!-- Global Settings -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="reference_no" class="form-label">Nomor Referensi</label>
                            <input type="text" class="form-control" id="reference_no" name="reference_no" 
                                   placeholder="Auto-generate jika kosong">
                        </div>
                        <div class="col-md-6">
                            <label for="global_notes" class="form-label">Catatan Global</label>
                            <input type="text" class="form-control" id="global_notes" name="global_notes" 
                                   placeholder="Alasan penyesuaian, stock opname, dll">
                        </div>
                    </div>

                    <!-- Product Selection -->
                    <div class="mb-4">
                        <h6>Pilih Produk untuk Penyesuaian</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <select class="form-select" id="productSelector">
                                    <option value="">Pilih produk untuk ditambahkan...</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?= $product['id'] ?>" 
                                                data-name="<?= esc($product['name']) ?>"
                                                data-sku="<?= $product['sku'] ?>"
                                                data-stock="<?= $product['current_stock'] ?>"
                                                data-unit="<?= $product['unit'] ?>"
                                                data-category="<?= esc($product['category_name']) ?>">
                                            <?= esc($product['name']) ?> (<?= $product['sku'] ?>) 
                                            - Stok: <?= number_format($product['current_stock']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" id="addProductBtn">
                                    <i class="bi bi-plus-circle"></i> Tambah Produk
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Adjustment Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="adjustmentTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="25%">Produk</th>
                                    <th width="15%">Stok Sistem</th>
                                    <th width="15%">Stok Fisik</th>
                                    <th width="15%">Selisih</th>
                                    <th width="20%">Catatan</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="adjustmentRows">
                                <!-- Dynamic rows will be added here -->
                            </tbody>
                        </table>
                    </div>

                    <div id="emptyState" class="text-center py-5">
                        <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Belum ada produk dipilih</h5>
                        <p class="text-muted">Pilih produk dari dropdown di atas untuk mulai penyesuaian stok</p>
                    </div>

                    <!-- Submit Section -->
                    <div class="row mt-4" id="submitSection" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-info-circle fs-4 me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1">Konfirmasi Penyesuaian</h6>
                                        <p class="mb-0">Pastikan stok fisik sudah dihitung dengan benar. Penyesuaian akan langsung mengubah stok sistem.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('/stock/history') ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali ke History
                                </a>
                                <button type="submit" class="btn btn-warning" id="submitBtn">
                                    <i class="bi bi-check-circle"></i> Terapkan Penyesuaian
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-12 col-lg-3">
        <!-- Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-calculator"></i> Ringkasan</h5>
            </div>
            <div class="card-body">
                <div class="summary-stats">
                    <div class="stat-item mb-3">
                        <label class="text-muted">Total Produk:</label>
                        <h4 class="text-primary" id="totalProducts">0</h4>
                    </div>
                    
                    <div class="stat-item mb-3">
                        <label class="text-muted">Total Penyesuaian:</label>
                        <h4 class="text-info" id="totalAdjustments">0</h4>
                    </div>
                    
                    <div class="stat-item mb-3">
                        <label class="text-muted">Penambahan:</label>
                        <h4 class="text-success" id="totalIncreases">0</h4>
                    </div>
                    
                    <div class="stat-item">
                        <label class="text-muted">Pengurangan:</label>
                        <h4 class="text-danger" id="totalDecreases">0</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-question-circle"></i> Panduan</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Langkah Penyesuaian:</h6>
                    <ol class="mb-0">
                        <li>Lakukan stock opname fisik</li>
                        <li>Pilih produk yang akan disesuaikan</li>
                        <li>Masukkan jumlah stok fisik</li>
                        <li>Sistem akan hitung selisih otomatis</li>
                        <li>Tambahkan catatan jika diperlukan</li>
                        <li>Terapkan penyesuaian</li>
                    </ol>
                </div>
                
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Peringatan:</h6>
                    <ul class="mb-0">
                        <li>Penyesuaian tidak dapat dibatalkan</li>
                        <li>Akan tercatat            $row.find('.current-stock').val(stock.toLocaleString() + ' ' + unit);
            $row.find('.product-info').html(`<i class="bi bi-box"></i> ${name} • SKU: ${sku}`);
        } else {
            $row.find('.current-stock').val('');
            $row.find('.product-info').html('');
        }
        
        updateSummary();
    });

    // Quantity change
    $(document).on('input', '.quantity-input', function() {
        updateSummary();
    });

    function addNewRow() {
        const newRow = `
            <tr class="movement-row">
                <td>
                    <select class="form-select product-select" name="movements[${rowIndex}][product_id]" required>
                        <option value="">Pilih Produk</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>" 
                                    data-stock="<?= $product['current_stock'] ?>"
                                    data-unit="<?= $product['unit'] ?>"
                                    data-name="<?= esc($product['name']) ?>"
                                    data-sku="<?= $product['sku'] ?>">
                                <?= esc($product['name']) ?> (<?= $product['sku'] ?>)
                            </option>
                        <?php endforeach ?>
                    </select>
                    <small class="product-info text-muted"></small>
                </td>
                <td>
                    <input type="text" class="form-control current-stock" readonly>
                </td>
                <td>
                    <input type="number" class="form-control quantity-input" 
                           name="movements[${rowIndex}][quantity]" min="1" placeholder="0" required>
                </td>
                <td>
                    <input type="text" class="form-control" name="movements[${rowIndex}][notes]" 
                           placeholder="Catatan khusus">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#movementRows').append(newRow);
        rowIndex++;
        updateRemoveButtons();
    }

    function updateRowIndexes() {
        $('#movementRows tr').each(function(index) {
            $(this).find('select, input').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/\[\d+\]/, '[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
        rowIndex = $('#movementRows tr').length;
    }

    function updateRemoveButtons() {
        const rowCount = $('#movementRows tr').length;
        $('.remove-row').prop('disabled', rowCount <= 1);
    }

    function updateSummary() {
        let totalItems = 0;
        let totalQuantity = 0;
        const products = [];

        $('.movement-row').each(function() {
            const $row = $(this);
            const productSelect = $row.find('.product-select');
            const quantityInput = $row.find('.quantity-input');
            
            const productName = productSelect.find('option:selected').data('name');
            const quantity = parseInt(quantityInput.val()) || 0;
            
            if (productSelect.val() && quantity > 0) {
                totalItems++;
                totalQuantity += quantity;
                products.push({
                    name: productName,
                    quantity: quantity
                });
            }
        });

        $('#totalItems').text(totalItems);
        $('#totalQuantity').text(totalQuantity.toLocaleString());

        // Update product list
        let productListHtml = '';
        if (products.length > 0) {
            productListHtml = products.map(p => 
                `<div class="d-flex justify-content-between">
                    <span>${p.name}</span>
                    <strong class="text-success">+${p.quantity.toLocaleString()}</strong>
                </div>`
            ).join('');
        } else {
            productListHtml = '<p class="text-muted"><em>Belum ada produk dipilih</em></p>';
        }
        $('#productList').html(productListHtml);
    }

    // Form submission
    $('#stockInForm').on('submit', function(e) {
        const hasValidRows = $('.movement-row').filter(function() {
            const productId = $(this).find('.product-select').val();
            const quantity = parseInt($(this).find('.quantity-input').val()) || 0;
            return productId && quantity > 0;
        }).length;

        if (hasValidRows === 0) {
            e.preventDefault();
            showError('Error', 'Minimal satu produk dengan jumlah yang valid harus diisi');
            return false;
        }

        $('#submitBtn').prop('disabled', true)
                       .html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
    });

    // Initialize
    updateRemoveButtons();
    updateSummary();
    
    // Auto-select product if from URL parameter
    <?php if ($selected_product): ?>
        $('.product-select').first().val('<?= $selected_product ?>').trigger('change');
    <?php endif ?>
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 15px;
    width: 2px;
    height: calc(100% + 10px);
    background-color: #e9ecef;
}

.movement-row {
    transition: all 0.3s ease;
}

.movement-row:hover {
    background-color: rgba(67, 94, 190, 0.05);
}

.product-info {
    font-size: 0.8rem;
    margin-top: 2px;
}

.summary-info {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.table-bordered td, .table-bordered th {
    vertical-align: middle;
}

.form-select:focus, .form-control:focus {
    border-color: #435ebe;
    box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
<?= $this->endSection() ?>