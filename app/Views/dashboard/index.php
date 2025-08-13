<!-- Setelah stats cards, tambahkan quick actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-lightning-fill"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="<?= base_url('/categories/create') ?>" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Tambah Kategori
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= base_url('/products/create') ?>" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle"></i> Tambah Produk
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= base_url('/stock/in') ?>" class="btn btn-info w-100">
                            <i class="bi bi-arrow-down-circle"></i> Barang Masuk
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= base_url('/stock/out') ?>" class="btn btn-warning w-100">
                            <i class="bi bi-arrow-up-circle"></i> Barang Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>