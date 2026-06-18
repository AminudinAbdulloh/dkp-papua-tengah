<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Pengaturan Latar Belakang Beranda</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Pengaturan Beranda</li>
    </ol>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/pengaturan-beranda/update') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row justify-content-center">
            <!-- 1. Beranda Hero -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white py-3">
                        <i class="bi bi-image me-1"></i> Latar Belakang Hero Beranda
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4 text-center">
                            <p class="text-muted fw-semibold mb-2">Latar Belakang Saat Ini</p>
                            <?php if (!empty($setting['body'])): ?>
                                <img src="<?= base_url($setting['body']) ?>" alt="Hero Background" class="img-fluid rounded border shadow-sm" style="max-height: 250px; object-fit: cover; width: 100%;">
                            <?php else: ?>
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="height: 250px; width: 100%;">
                                    <i class="bi bi-image fs-1 me-2"></i> Menggunakan gambar default
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="hero_bg" class="form-label fw-semibold">Ubah Latar Belakang (Opsional)</label>
                            <input class="form-control" type="file" id="hero_bg" name="hero_bg" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text mt-2 text-muted">Maksimal 4MB. Rekomendasi resolusi: 2400x1000px.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-end py-3">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-1"></i> Simpan Pengaturan Beranda</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
