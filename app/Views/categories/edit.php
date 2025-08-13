<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4>Edit Kategori</h4>
                <p class="text-muted mb-0">Edit kategori: <strong><?= esc($category['name']) ?></strong></p>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/categories/update/' . $category['id']) ?>" method="POST" id="categoryForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= (session('errors.name')) ? 'is-invalid' : '' ?>" 
                                       id="name" 
                                       name="name" 
                                       value="<?= old('name', $category['name']) ?>"
                                       placeholder="Masukkan nama kategori"
                                       required>
                                <?php if (session('errors.name')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.name') ?>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control <?= (session('errors.description')) ? 'is-invalid' : '' ?>" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Deskripsi kategori (opsional)"><?= old('description', $category['description']) ?></textarea>
                                <?php if (session('errors.description')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.description') ?>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           <?= old('is_active', $category['is_active']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">
                                        Kategori Aktif
                                    </label>
                                </div>
                                <small class="text-muted">Kategori aktif akan ditampilkan dalam pilihan saat membuat produk</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> 
                                        Dibuat: <?= date('d M Y H:i', strtotime($category['created_at'])) ?>
                                        <?php if ($category['updated_at'] && $category['updated_at'] != $category['created_at']): ?>
                                            <br>
                                            <i class="bi bi-pencil"></i> 
                                            Diupdate: <?= date('d M Y H:i', strtotime($category['updated_at'])) ?>
                                        <?php endif ?>
                                    </small>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('/categories') ?>" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                                        <i class="bi bi-save"></i> Update Kategori
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
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-info-circle"></i> Informasi Kategori</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">ID Kategori</label>
                        <p class="fw-bold"><?= $category['id'] ?></p>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted">Status Saat Ini</label>
                        <div>
                            <?php if ($category['is_active']): ?>
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-check-circle"></i> Aktif
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary fs-6">
                                    <i class="bi bi-x-circle"></i> Nonaktif
                                </span>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Peringatan:</h6>
                    <p class="mb-0">Mengubah nama kategori akan mempengaruhi semua produk yang menggunakan kategori ini.</p>
                </div>
            </div>
        </div>
        
        <!-- Usage Statistics -->
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-bar-chart"></i> Statistik Penggunaan</h5>
            </div>
            <div class="card-body">
                <?php
                $productModel = new \App\Models\ProductModel();
                $productCount = $productModel->where('category_id', $category['id'])->countAllResults();
                $activeProducts = $productModel->where('category_id', $category['id'])
                                              ->where('is_active', true)
                                              ->countAllResults();
                ?>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="card-statistic">
                            <h4 class="text-primary"><?= $productCount ?></h4>
                            <p class="text-muted mb-0">Total Produk</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card-statistic">
                            <h4 class="text-success"><?= $activeProducts ?></h4>
                            <p class="text-muted mb-0">Produk Aktif</p>
                        </div>
                    </div>
                </div>
                
                <?php if ($productCount > 0): ?>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-exclamation-triangle"></i> 
                        Kategori ini digunakan oleh <?= $productCount ?> produk dan tidak bisa dihapus.
                    </small>
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
    <li class="breadcrumb-item"><a href="<?= base_url('/categories') ?>">Kategori</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Form submission with loading state
    $('#categoryForm').on('submit', function() {
        $('#btnSubmit').prop('disabled', true)
                       .html('<span class="spinner-border spinner-border-sm me-2"></span>Mengupdate...');
    });

    // Auto focus on name field
    $('#name').focus().select();
    
    // Highlight changes
    const originalName = "<?= $category['name'] ?>";
    const originalDesc = "<?= $category['description'] ?>";
    const originalStatus = <?= $category['is_active'] ? 'true' : 'false' ?>;
    
    function checkChanges() {
        const currentName = $('#name').val();
        const currentDesc = $('#description').val();
        const currentStatus = $('#is_active').is(':checked');
        
        const hasChanges = (currentName !== originalName) || 
                          (currentDesc !== originalDesc) || 
                          (currentStatus !== originalStatus);
        
        if (hasChanges) {
            $('#btnSubmit').removeClass('btn-primary').addClass('btn-warning')
                          .html('<i class="bi bi-save"></i> Update Kategori (Ada Perubahan)');
        } else {
            $('#btnSubmit').removeClass('btn-warning').addClass('btn-primary')
                          .html('<i class="bi bi-save"></i> Update Kategori');
        }
    }
    
    $('#name, #description, #is_active').on('input change', checkChanges);
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.card-statistic h4 {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.form-control:focus {
    border-color: #435ebe;
    box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
}

.btn-primary {
    background-color: #435ebe;
    border-color: #435ebe;
}

.btn-primary:hover {
    background-color: #364296;
    border-color: #364296;
}

.btn-warning {
    animation: pulse-warning 2s infinite;
}

@keyframes pulse-warning {
    0% { opacity: 1; }
    50% { opacity: 0.8; }
    100% { opacity: 1; }
}
</style>
<?= $this->endSection() ?>