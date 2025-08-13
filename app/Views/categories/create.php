<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4>Tambah Kategori Baru</h4>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/categories/store') ?>" method="POST" id="categoryForm">
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
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?= base_url('/categories') ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnSubmit">
                                    <i class="bi bi-save"></i> Simpan Kategori
                                </button>
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
                <h5><i class="bi bi-info-circle"></i> Informasi</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Tips Kategori:</h6>
                    <ul class="mb-0">
                        <li>Gunakan nama yang jelas dan mudah dipahami</li>
                        <li>Nama kategori harus unik</li>
                        <li>Deskripsi membantu penjelasan lebih detail</li>
                        <li>Kategori nonaktif tidak akan muncul dalam pilihan</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Perhatian:</h6>
                    <p class="mb-0">Kategori yang sudah digunakan oleh produk tidak bisa dihapus.</p>
                </div>
            </div>
        </div>
        
        <!-- Preview Card -->
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-eye"></i> Preview</h5>
            </div>
            <div class="card-body">
                <div class="preview-category">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-content bg-primary text-white">
                                <i class="bi bi-collection-fill"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0" id="preview-name">Nama Kategori</h6>
                            <small class="text-muted">Baru dibuat</small>
                        </div>
                    </div>
                    <p class="mb-2" id="preview-description">
                        <em class="text-muted">Tidak ada deskripsi</em>
                    </p>
                    <span class="badge bg-success" id="preview-status">
                        <i class="bi bi-check-circle"></i> Aktif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('/categories') ?>">Kategori</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Real-time preview
    $('#name').on('input', function() {
        const name = $(this).val() || 'Nama Kategori';
        $('#preview-name').text(name);
    });
    
    $('#description').on('input', function() {
        const description = $(this).val();
        if (description.trim() !== '') {
            $('#preview-description').text(description);
        } else {
            $('#preview-description').html('<em class="text-muted">Tidak ada deskripsi</em>');
        }
    });
    
    $('#is_active').on('change', function() {
        if ($(this).is(':checked')) {
            $('#preview-status').removeClass('bg-secondary').addClass('bg-success')
                .html('<i class="bi bi-check-circle"></i> Aktif');
        } else {
            $('#preview-status').removeClass('bg-success').addClass('bg-secondary')
                .html('<i class="bi bi-x-circle"></i> Nonaktif');
        }
    });

    // Form submission with loading state
    $('#categoryForm').on('submit', function() {
        $('#btnSubmit').prop('disabled', true)
                       .html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
    });

    // Auto focus on name field
    $('#name').focus();
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.preview-category {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    background-color: #f8f9fa;
}

.avatar-content {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
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
</style>
<?= $this->endSection() ?>