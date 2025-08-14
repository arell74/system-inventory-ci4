<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<!-- Header with Quick Stats -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1">
                            <i class="bi bi-arrow-up-circle text-warning"></i>
                            Barang Keluar
                        </h4>
                        <p class="text-muted mb-0">Output stok barang dari inventory</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="row text-center">
                            <div class="col-6">
                                <h5 class="text-warning mb-0"><?= count($products) ?></h5>
                                <small class="text-muted">Produk Tersedia</small>
                            </div>
                            <div class="col-6">
                                <h5 class="text-info mb-0"><?= count($recent_movements) ?></h5>
                                <small class="text-muted">Transaksi Hari Ini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Form -->
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-minus-circle"></i> Form Barang Keluar</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/stock/out/store') ?>" method="POST" id="stockOutForm">
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
                                placeholder="Tujuan, customer, dll">
                        </div>
                    </div>

                    <!-- Product Selection Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="movementTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="35%">Produk</th>
                                    <th width="15%">Stok Tersedia</th>
                                    <th width="15%">Jumlah Keluar</th>
                                    <th width="25%">Catatan</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="movementRows">
                                <tr class="movement-row">
                                    <td>
                                        <select class="form-select product-select" name="movements[0][product_id]" required>
                                            <option value="">Pilih Produk</option>
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?= $product['id'] ?>"
                                                    data-stock="<?= $product['current_stock'] ?>"
                                                    data-unit="<?= $product['unit'] ?>"
                                                    data-name="<?= esc($product['name']) ?>"
                                                    data-sku="<?= $product['sku'] ?>"
                                                    data-min="<?= $product['min_stock'] ?>"
                                                    <?= ($selected_product == $product['id']) ? 'selected' : '' ?>>
                                                    <?= esc($product['name']) ?> (<?= $product['sku'] ?>)
                                                    - Stok: <?= number_format($product['current_stock']) ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                        <small class="product-info text-muted"></small>
                                    </td>
                                    <td>
                                        <div class="stock-display">
                                            <input type="text" class="form-control current-stock" readonly>
                                            <div class="stock-indicator mt-1"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity-input"
                                            name="movements[0][quantity]" min="1" placeholder="0" required>
                                        <small class="remaining-stock text-muted"></small>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="movements[0][notes]"
                                            placeholder="Tujuan, customer, dll">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Stock Warnings -->
                    <div id="stockWarnings" class="mb-3"></div>

                    <!-- Add Row & Submit -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="button" class="btn btn-outline-primary" id="addRowBtn">
                            <i class="bi bi-plus-circle"></i> Tambah Produk
                        </button>

                        <div class="btn-group">
                            <a href="<?= base_url('/stock/history') ?>" class="btn btn-secondary">
                                <i class="bi bi-clock-history"></i> History
                            </a>
                            <button type="submit" class="btn btn-warning" id="submitBtn">
                                <i class="bi bi-save"></i> Keluarkan Stok
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-12 col-lg-4">
        <!-- Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-calculator"></i> Ringkasan Transaksi</h5>
            </div>
            <div class="card-body">
                <div class="summary-info">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted">Total Item:</label>
                            <h4 class="text-primary" id="totalItems">0</h4>
                        </div>
                        <div class="col-6">
                            <label class="text-muted">Total Quantity:</label>
                            <h4 class="text-warning" id="totalQuantity">0</h4>
                        </div>
                    </div>

                    <div id="selectedProducts" class="mt-3">
                        <h6 class="text-muted">Produk Dipilih:</h6>
                        <div id="productList">
                            <p class="text-muted"><em>Belum ada produk dipilih</em></p>
                        </div>
                    </div>

                    <div id="totalValue" class="mt-3 d-none">
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Estimasi Nilai:</span>
                                <h5 class="text-danger mb-0" id="totalValueAmount">Rp 0</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-exclamation-triangle text-warning"></i> Peringatan</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Perhatian!</h6>
                    <ul class="mb-0">
                        <li>Pastikan stok fisik tersedia</li>
                        <li>Cek ulang jumlah yang akan dikeluarkan</li>
                        <li>Perhatikan batas minimum stok</li>
                        <li>Catat tujuan atau customer</li>
                    </ul>
                </div>

                <div id="lowStockAlert" class="alert alert-danger d-none">
                    <h6 class="alert-heading">
                        <i class="bi bi-exclamation-triangle"></i> Stok Akan Rendah!
                    </h6>
                    <div id="lowStockItems"></div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <?php if (!empty($recent_movements)): ?>
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-clock"></i> Transaksi Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($recent_movements as $movement): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1"><?= esc($movement['product_name']) ?></h6>
                                    <p class="mb-0">
                                        <strong class="text-danger">-<?= number_format($movement['quantity']) ?></strong>
                                        <small class="text-muted">
                                            • <?= time_ago($movement['created_at']) ?>
                                        </small>
                                    </p>
                                    <?php if ($movement['reference_no']): ?>
                                        <small class="text-muted">Ref: <?= $movement['reference_no'] ?></small>
                                    <?php endif ?>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('/stock/history') ?>">Stock</a></li>
    <li class="breadcrumb-item active" aria-current="page">Barang Keluar</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        let rowIndex = 1;

        // Add new row
        $('#addRowBtn').on('click', function() {
            addNewRow();
        });

        // Remove row
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            updateRowIndexes();
            updateSummary();
            updateRemoveButtons();
            checkStockWarnings();
        });

        // Product selection change
        $(document).on('change', '.product-select', function() {
            const $row = $(this).closest('tr');
            const selectedOption = $(this).find('option:selected');

            if (selectedOption.val()) {
                const stock = parseInt(selectedOption.data('stock'));
                const unit = selectedOption.data('unit');
                const name = selectedOption.data('name');
                const sku = selectedOption.data('sku');
                const minStock = parseInt(selectedOption.data('min'));

                $row.find('.current-stock').val(stock.toLocaleString() + ' ' + unit);
                $row.find('.product-info').html(`<i class="bi bi-box"></i> ${name} • SKU: ${sku}`);
                $row.find('.quantity-input').attr('max', stock);

                // Stock indicator
                let indicator = '';
                if (stock == 0) {
                    indicator = '<span class="badge bg-danger">Habis</span>';
                } else if (stock <= minStock) {
                    indicator = '<span class="badge bg-warning">Rendah</span>';
                } else {
                    indicator = '<span class="badge bg-success">Normal</span>';
                }
                $row.find('.stock-indicator').html(indicator);
            } else {
                $row.find('.current-stock').val('');
                $row.find('.product-info').html('');
                $row.find('.stock-indicator').html('');
                $row.find('.remaining-stock').html('');
                $row.find('.quantity-input').removeAttr('max');
            }

            updateSummary();
            checkStockWarnings();
        });

        // Quantity change
        $(document).on('input', '.quantity-input', function() {
            const $row = $(this).closest('tr');
            const quantity = parseInt($(this).val()) || 0;
            const selectedOption = $row.find('.product-select option:selected');
            const availableStock = parseInt(selectedOption.data('stock')) || 0;

            if (quantity > 0 && availableStock > 0) {
                const remaining = availableStock - quantity;
                let remainingText = `Sisa: ${remaining.toLocaleString()}`;
                let remainingClass = 'text-muted';

                if (remaining < 0) {
                    remainingText = `Kekurangan: ${Math.abs(remaining).toLocaleString()}`;
                    remainingClass = 'text-danger';
                    $(this).addClass('is-invalid');
                } else if (remaining <= parseInt(selectedOption.data('min'))) {
                    remainingClass = 'text-warning';
                    $(this).removeClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }

                $row.find('.remaining-stock').html(remainingText).removeClass().addClass(`remaining-stock ${remainingClass}`);
            } else {
                $row.find('.remaining-stock').html('');
                $(this).removeClass('is-invalid');
            }

            updateSummary();
            checkStockWarnings();
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
                                    data-sku="<?= $product['sku'] ?>"
                                    data-min="<?= $product['min_stock'] ?>">
                                <?= esc($product['name']) ?> (<?= $product['sku'] ?>) 
                                - Stok: <?= number_format($product['current_stock']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <small class="product-info text-muted"></small>
                </td>
                <td>
                    <div class="stock-display">
                        <input type="text" class="form-control current-stock" readonly>
                        <div class="stock-indicator mt-1"></div>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control quantity-input" 
                           name="movements[${rowIndex}][quantity]" min="1" placeholder="0" required>
                    <small class="remaining-stock text-muted"></small>
                </td>
                <td>
                    <input type="text" class="form-control" name="movements[${rowIndex}][notes]" 
                           placeholder="Tujuan, customer, dll">
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
            let totalValue = 0;
            const products = [];

            $('.movement-row').each(function() {
                const $row = $(this);
                const productSelect = $row.find('.product-select');
                const quantityInput = $row.find('.quantity-input');
                const selectedOption = productSelect.find('option:selected');

                const productName = selectedOption.data('name');
                const quantity = parseInt(quantityInput.val()) || 0;

                if (productSelect.val() && quantity > 0) {
                    totalItems++;
                    totalQuantity += quantity;

                    // Add estimated value calculation here if needed
                    // const price = selectedOption.data('price') || 0;
                    // totalValue += quantity * price;

                    products.push({
                        name: productName,
                        quantity: quantity
                    });
                }
            });

            $('#totalItems').text(totalItems);
            $('#totalQuantity').text(totalQuantity.toLocaleString());
            $('#totalValueAmount').text(formatCurrency(totalValue));

            if (totalValue > 0) {
                $('#totalValue').removeClass('d-none');
            } else {
                $('#totalValue').addClass('d-none');
            }

            // Update product list
            let productListHtml = '';
            if (products.length > 0) {
                productListHtml = products.map(p =>
                    `<div class="d-flex justify-content-between">
                    <span>${p.name}</span>
                    <strong class="text-danger">-${p.quantity.toLocaleString()}</strong>
                </div>`
                ).join('');
            } else {
                productListHtml = '<p class="text-muted"><em>Belum ada produk dipilih</em></p>';
            }
            $('#productList').html(productListHtml);
        }

        function checkStockWarnings() {
            const warnings = [];
            let hasLowStock = false;

            $('.movement-row').each(function() {
                const $row = $(this);
                const selectedOption = $row.find('.product-select option:selected');
                const quantity = parseInt($row.find('.quantity-input').val()) || 0;

                if (selectedOption.val() && quantity > 0) {
                    const stock = parseInt(selectedOption.data('stock'));
                    const minStock = parseInt(selectedOption.data('min'));
                    const productName = selectedOption.data('name');
                    const remaining = stock - quantity;

                    if (remaining < 0) {
                        warnings.push(`<strong>${productName}:</strong> Stok tidak mencukupi (kekurangan ${Math.abs(remaining)})`);
                    } else if (remaining <= minStock && remaining > 0) {
                        warnings.push(`<strong>${productName}:</strong> Stok akan rendah setelah transaksi (sisa ${remaining})`);
                        hasLowStock = true;
                    }
                }
            });

            // Display warnings
            if (warnings.length > 0) {
                let warningHtml = '<div class="alert alert-warning"><ul class="mb-0">';
                warnings.forEach(warning => {
                    warningHtml += `<li>${warning}</li>`;
                });
                warningHtml += '</ul></div>';
                $('#stockWarnings').html(warningHtml);
            } else {
                $('#stockWarnings').html('');
            }

            // Update low stock alert
            if (hasLowStock) {
                $('#lowStockAlert').removeClass('d-none');
                const lowStockItems = warnings.filter(w => w.includes('akan rendah'));
                $('#lowStockItems').html('<ul class="mb-0">' + lowStockItems.map(w => `<li>${w}</li>`).join('') + '</ul>');
            } else {
                $('#lowStockAlert').addClass('d-none');
            }
        }

        // Form submission
        $('#stockOutForm').on('submit', function(e) {
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

            // Check for invalid quantities
            const hasInvalidQuantities = $('.quantity-input.is-invalid').length > 0;
            if (hasInvalidQuantities) {
                e.preventDefault();
                showError('Error', 'Ada produk dengan jumlah melebihi stok yang tersedia');
                return false;
            }

            $('#submitBtn').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
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

    .movement-row {
        transition: all 0.3s ease;
    }

    .movement-row:hover {
        background-color: rgba(255, 193, 7, 0.05);
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

    .stock-indicator {
        height: 20px;
    }

    .remaining-stock {
        font-size: 0.8rem;
        margin-top: 2px;
    }

    .quantity-input.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .table-bordered td,
    .table-bordered th {
        vertical-align: middle;
    }

    .form-select:focus,
    .form-control:focus {
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