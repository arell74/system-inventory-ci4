<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Daftar Kategori</h4>
                <a href="<?= base_url('/categories/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Kategori
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="categoriesTable">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">Nama Kategori</th>
                                <th width="35%">Deskripsi</th>
                                <th width="10%">Jumlah Produk</th>
                                <th width="10%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $index => $category): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="avatar-content bg-primary text-white">
                                                <i class="bi bi-collection-fill"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?= esc($category['name']) ?></h6>
                                            <small class="text-muted">
                                                Dibuat: <?= date('d M Y', strtotime($category['created_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-0"><?= esc($category['description']) ?: '<em class="text-muted">Tidak ada deskripsi</em>' ?></p>
                                </td>
                                <td>
                                    <span class="badge bg-info fs-6">
                                        <?= $category['product_count'] ?> produk
                                    </span>
                                </td>
                                <td>
                                    <?php if ($category['is_active']): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-x-circle"></i> Nonaktif
                                        </span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('/categories/edit/' . $category['id']) ?>" 
                                           class="btn btn-sm btn-warning" 
                                           data-bs-toggle="tooltip" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger btn-delete" 
                                                data-id="<?= $category['id'] ?>"
                                                data-name="<?= esc($category['name']) ?>"
                                                data-products="<?= $category['product_count'] ?>"
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
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Kategori</li>
</ol>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#categoriesTable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        columnDefs: [
            { orderable: false, targets: [5] } // Disable ordering on action column
        ]
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Delete category handler
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const productCount = $(this).data('products');
        
        if (productCount > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Bisa Hapus!',
                text: `Kategori "${name}" masih digunakan oleh ${productCount} produk.`,
                confirmButtonText: 'OK',
                confirmButtonColor: '#435ebe'
            });
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Konfirmasi Hapus',
            text: `Yakin ingin menghapus kategori "${name}"?`,
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteCategory(id, name);
            }
        });
    });

    function deleteCategory(id, name) {
        $.ajax({
            url: `<?= base_url('/categories/delete/') ?>${id}`,
            type: 'DELETE',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message,
                        confirmButtonColor: '#435ebe'
                    });
                }
            },
            error: function(xhr, status, error) {
                let message = 'Terjadi kesalahan saat menghapus kategori';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: message,
                    confirmButtonColor: '#435ebe'
                });
            }
        });
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
.avatar-content {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
<?= $this->endSection() ?>