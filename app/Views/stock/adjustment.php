<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
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
    <div class="col-12 col-lg-9">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-clipboard-check"></i> Form Penyesuaian Stok</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/stock/adjustment/store') ?>" method="POST" id="adjustmentForm">
                    <?= csrf_field() ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="reference_no" class="form-label">Nomor Referensi</label>
                            <input type="text" class="form-control" id="reference_no" name="reference_no" placeholder="Auto-generate jika kosong">
                        </div>
                        <div class="col-md-6">
                            <label for="global_notes" class="form-label">Catatan Global</label>
                            <input type="text" class="form-control" id="global_notes" name="global_notes" placeholder="Alasan penyesuaian, stock opname, dll">
                        </div>
                    </div>

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
                                            <?= esc($product['name']) ?> (<?= $product['sku'] ?>) - Stok: <?= number_format($product['current_stock']) ?>
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
                                </tbody>
                        </table>
                    </div>

                    <div id="emptyState" class="text-center py-5">
                        <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Belum ada produk dipilih</h5>
                        <p class="text-muted">Pilih produk dari dropdown di atas untuk mulai penyesuaian stok</p>
                    </div>

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

    <div class="col-12 col-lg-3">
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
                        <li>Akan tercatat sebagai histori</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('/stock/history') ?>">Stock</a></li>
    <li class="breadcrumb-item active" aria-current="page">Penyesuaian</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let adjustmentIndex = 0;

    // Add product to adjustment table
    $('#addProductBtn').on('click', function() {
        const selectedOption = $('#productSelector option:selected');
        const productId = selectedOption.val();
        
        if (!productId) {
            showError('Error', 'Pilih produk terlebih dahulu');
            return;
        }

        // Check if product already added
        if ($(`input[name="adjustments[${productId}][product_id]"]`).length > 0) {
            showError('Error', 'Produk sudah ditambahkan');
            return;
        }

        const productData = {
            id: productId,
            name: selectedOption.data('name'),
            sku: selectedOption.data('sku'),
            currentStock: parseInt(selectedOption.data('stock')),
            unit: selectedOption.data('unit'),
            category: selectedOption.data('category')
        };

        addAdjustmentRow(productData);
        
        // Reset selector
        $('#productSelector').val('');
        
        updateSummary();
        toggleEmptyState();
    });

    function addAdjustmentRow(product) {
        const row = `
            <tr class="adjustment-row" data-product-id="${product.id}">
                <td>
                    <input type="hidden" name="adjustments[${adjustmentIndex}][product_id]" value="${product.id}">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                            <div class="avatar-content bg-primary text-white">
                                <i class="bi bi-box"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">${product.name}</h6>
                            <small class="text-muted">${product.sku} • ${product.category}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="text-center">
                        <strong class="current-stock">${product.currentStock.toLocaleString()}</strong>
                        <small class="d-block text-muted">${product.unit}</small>
                    </div>
                </td>
                <td>
                    <input type="number" 
                           class="form-control text-center physical-stock" 
                           name="adjustments[${adjustmentIndex}][new_stock]" 
                           min="0" 
                           placeholder="${product.currentStock}"
                           data-current="${product.currentStock}"
                           required>
                </td>
                <td>
                    <div class="text-center difference-display">
                        <span class="difference-value">-</span>
                        <small class="d-block text-muted difference-type"></small>
                    </div>
                </td>
                <td>
                    <input type="text" 
                           class="form-control" 
                           name="adjustments[${adjustmentIndex}][notes]" 
                           placeholder="Alasan penyesuaian">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-adjustment">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#adjustmentRows').append(row);
        adjustmentIndex++;
    }

    // Remove adjustment row
    $(document).on('click', '.remove-adjustment', function() {
        $(this).closest('tr').remove();
        updateSummary();
        toggleEmptyState();
    });

    // Calculate difference when physical stock changes
    $(document).on('input', '.physical-stock', function() {
        const $row = $(this).closest('tr');
        const currentStock = parseInt($(this).data('current'));
        const physicalStock = parseInt($(this).val()) || 0;
        const difference = physicalStock - currentStock;
        
        const $differenceDisplay = $row.find('.difference-display');
        const $differenceValue = $differenceDisplay.find('.difference-value');
        const $differenceType = $differenceDisplay.find('.difference-type');
        
        if (difference === 0) {
            $differenceValue.text('0').removeClass().addClass('difference-value text-muted');
            $differenceType.text('Tidak ada perubahan');
        } else if (difference > 0) {
            $differenceValue.text(`+${difference.toLocaleString()}`).removeClass().addClass('difference-value text-success fw-bold');
            $differenceType.text('Penambahan');
        } else {
            $differenceValue.text(difference.toLocaleString()).removeClass().addClass('difference-value text-danger fw-bold');
            $differenceType.text('Pengurangan');
        }
        
        updateSummary();
    });

    function updateSummary() {
        let totalProducts = 0;
        let totalAdjustments = 0;
        let totalIncreases = 0;
        let totalDecreases = 0;

        $('.adjustment-row').each(function() {
            const $row = $(this);
            const currentStock = parseInt($row.find('.physical-stock').data('current'));
            const physicalStock = parseInt($row.find('.physical-stock').val()) || currentStock;
            const difference = physicalStock - currentStock;
            
            totalProducts++;
            
            if (difference !== 0) {
                totalAdjustments++;
                if (difference > 0) {
                    totalIncreases += difference;
                } else {
                    totalDecreases += Math.abs(difference);
                }
            }
        });

        $('#totalProducts').text(totalProducts);
        $('#totalAdjustments').text(totalAdjustments);
        $('#totalIncreases').text(totalIncreases.toLocaleString());
        $('#totalDecreases').text(totalDecreases.toLocaleString());
    }

    function toggleEmptyState() {
        const hasRows = $('#adjustmentRows tr').length > 0;
        
        if (hasRows) {
            $('#emptyState').hide();
            $('#adjustmentTable').show();
            $('#submitSection').show();
        } else {
            $('#emptyState').show();
            $('#adjustmentTable').hide();
            $('#submitSection').hide();
        }
    }

    // Form submission
    $('#adjustmentForm').on('submit', function(e) {
        const hasAdjustments = $('.adjustment-row').length > 0;
        
        if (!hasAdjustments) {
            e.preventDefault();
            showError('Error', 'Minimal satu produk harus dipilih untuk penyesuaian');
            return false;
        }

        // Check if any actual adjustments (differences) exist
        let hasChanges = false;
        $('.adjustment-row').each(function() {
            const currentStock = parseInt($(this).find('.physical-stock').data('current'));
            const physicalStock = parseInt($(this).find('.physical-stock').val()) || currentStock;
            
            if (physicalStock !== currentStock) {
                hasChanges = true;
                return false; // break loop
            }
        });

        if (!hasChanges) {
            e.preventDefault();
            showError('Error', 'Tidak ada perubahan stok untuk disesuaikan');
            return false;
        }

        // Confirm before submission
        const totalAdjustments = parseInt($('#totalAdjustments').text());
        const totalIncreases = parseInt($('#totalIncreases').text().replace(/,/g, ''));
        const totalDecreases = parseInt($('#totalDecreases').text().replace(/,/g, ''));
        
        let confirmMessage = `Akan melakukan ${totalAdjustments} penyesuaian stok:\n`;
        if (totalIncreases > 0) {
            confirmMessage += `• Penambahan: ${totalIncreases.toLocaleString()} item\n`;
        }
        if (totalDecreases > 0) {
            confirmMessage += `• Pengurangan: ${totalDecreases.toLocaleString()} item\n`;
        }
        confirmMessage += '\nPenyesuaian tidak dapat dibatalkan. Lanjutkan?';

        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return false;
        }

        $('#submitBtn').prop('disabled', true)
                       .html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
    });

    // Initialize
    toggleEmptyState();
    updateSummary();
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
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

.summary-stats {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.stat-item {
    text-align: center;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.stat-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.stat-item h4 {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0;
}

.adjustment-row {
    transition: all 0.3s ease;
}

.adjustment-row:hover {
    background-color: rgba(67, 94, 190, 0.05);
}

.difference-display {
    min-height: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.difference-value {
    font-size: 1.1rem;
}

.table-bordered td, .table-bordered th {
    vertical-align: middle;
}

.physical-stock:focus {
    border-color: #435ebe;
    box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
}

.border-warning {
    border-color: #ffc107 !important;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .stat-item h4 {
        font-size: 1.5rem;
    }
    
    .avatar-content {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
}
</style>
<?= $this->endSection() ?>