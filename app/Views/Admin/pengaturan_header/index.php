<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Pengaturan Latar Belakang Header</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Pengaturan Header</li>
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

    <form action="<?= base_url('admin/pengaturan-header/update') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row">
            <!-- 1. Header Profil -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <i class="bi bi-building me-1"></i> Header Profil
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Digunakan pada: Sejarah, Visi Misi, Tupoksi, Struktur, Pejabat, Alamat & Kontak</p>
                        <div class="mb-4 text-center">
                            <p class="text-muted mb-2 fw-semibold">Latar Belakang Saat Ini</p>
                            <?php if (!empty($headerProfil['body'])): ?>
                                <img src="<?= base_url($headerProfil['body']) ?>" alt="Profil Header" class="img-fluid rounded border shadow-sm" style="max-height: 150px; object-fit: cover; width: 100%;">
                            <?php else: ?>
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="height: 150px; width: 100%;">
                                    <i class="bi bi-image fs-1 me-2"></i> Menggunakan default: header_profil.png
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="header_profil" class="form-label fw-semibold">Ubah Latar Belakang (Opsional)</label>
                            <input class="form-control" type="file" id="header_profil" name="header_profil" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text mt-2 text-muted">Maksimal 4MB. Rekomendasi: 1920x450px.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Header Informasi -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-success text-white py-3">
                        <i class="bi bi-info-circle me-1"></i> Header Informasi
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Digunakan pada: Berita dan Pengumuman</p>
                        <div class="mb-4 text-center">
                            <p class="text-muted mb-2 fw-semibold">Latar Belakang Saat Ini</p>
                            <?php if (!empty($headerInformasi['body'])): ?>
                                <img src="<?= base_url($headerInformasi['body']) ?>" alt="Informasi Header" class="img-fluid rounded border shadow-sm" style="max-height: 150px; object-fit: cover; width: 100%;">
                            <?php else: ?>
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="height: 150px; width: 100%;">
                                    <i class="bi bi-image fs-1 me-2"></i> Menggunakan default: header_informasi.png
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="header_informasi" class="form-label fw-semibold">Ubah Latar Belakang (Opsional)</label>
                            <input class="form-control" type="file" id="header_informasi" name="header_informasi" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text mt-2 text-muted">Maksimal 4MB. Rekomendasi: 1920x450px.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Header Galeri -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-info text-white py-3">
                        <i class="bi bi-images me-1"></i> Header Galeri
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Digunakan pada: Galeri Foto dan Galeri Video</p>
                        <div class="mb-4 text-center">
                            <p class="text-muted mb-2 fw-semibold">Latar Belakang Saat Ini</p>
                            <?php if (!empty($headerGaleri['body'])): ?>
                                <img src="<?= base_url($headerGaleri['body']) ?>" alt="Galeri Header" class="img-fluid rounded border shadow-sm" style="max-height: 150px; object-fit: cover; width: 100%;">
                            <?php else: ?>
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="height: 150px; width: 100%;">
                                    <i class="bi bi-image fs-1 me-2"></i> Menggunakan default: header_galeri.png
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="header_galeri" class="form-label fw-semibold">Ubah Latar Belakang (Opsional)</label>
                            <input class="form-control" type="file" id="header_galeri" name="header_galeri" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text mt-2 text-muted">Maksimal 4MB. Rekomendasi: 1920x450px.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Header PPID -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-secondary text-white py-3">
                        <i class="bi bi-folder-fill me-1"></i> Header PPID
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Digunakan pada: Publikasi, Layanan PPID, Informasi Publik, Download</p>
                        <div class="mb-4 text-center">
                            <p class="text-muted mb-2 fw-semibold">Latar Belakang Saat Ini</p>
                            <?php if (!empty($headerPpid['body'])): ?>
                                <img src="<?= base_url($headerPpid['body']) ?>" alt="PPID Header" class="img-fluid rounded border shadow-sm" style="max-height: 150px; object-fit: cover; width: 100%;">
                            <?php else: ?>
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="height: 150px; width: 100%;">
                                    <i class="bi bi-image fs-1 me-2"></i> Menggunakan default: header_ppid.png
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="header_ppid" class="form-label fw-semibold">Ubah Latar Belakang (Opsional)</label>
                            <input class="form-control" type="file" id="header_ppid" name="header_ppid" accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="form-text mt-2 text-muted">Maksimal 4MB. Rekomendasi: 1920x450px.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-body text-end py-3">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-1"></i> Simpan Pengaturan Header</button>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
