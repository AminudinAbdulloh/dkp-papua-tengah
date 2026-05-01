<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?><?= isset($download) ? 'Edit Dokumen' : 'Tambah Dokumen' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('admin/konten/download') ?>">Download</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= isset($download) ? 'Edit' : 'Tambah' ?></li>
        </ol>
    </nav>
    <h1 class="h3 fw-bold text-body mb-1"><?= isset($download) ? 'Edit Dokumen' : 'Tambah Dokumen Baru' ?></h1>
    <p class="text-secondary mb-0">Pastikan file yang diunggah aman dan sesuai dengan kategori informasi publik.</p>
</div>

<?php if ($errors = session()->getFlashdata('errors')) : ?>
    <div class="alert alert-danger rounded-3 mb-4">
        <ul class="mb-0">
            <?php foreach ($errors as $error) : ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-lg-5">
        <form action="<?= isset($download) ? base_url('admin/konten/download/' . $download['id'] . '/update') : base_url('admin/konten/download/simpan') ?>" 
              method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <div class="mb-4">
                        <label for="title" class="form-label fw-semibold">Nama Dokumen / Judul</label>
                        <input type="text" class="form-control rounded-3" id="title" name="title" 
                            placeholder="Contoh: Laporan Tahunan 2023"
                            value="<?= old('title', $download['title'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card bg-light border-0 rounded-4">
                        <div class="card-body p-4">
                            <label for="file" class="form-label fw-semibold">Pilih File</label>
                            <input type="file" class="form-control rounded-3 mb-2" id="file" name="file" 
                                <?= isset($download) ? '' : 'required' ?>>
                            
                            <div class="small text-secondary mb-3">
                                <i class="bi bi-info-circle me-1"></i> Format: PDF, DOCX, XLSX, ZIP. Maks: 20MB.
                            </div>

                            <?php if (isset($download)) : ?>
                                <div class="p-3 bg-white border rounded-3 small">
                                    <div class="text-muted mb-1">File saat ini:</div>
                                    <div class="fw-bold text-truncate" title="<?= esc($download['file_path']) ?>">
                                        <i class="bi bi-file-earmark-check me-1"></i><?= esc($download['file_path']) ?>
                                    </div>
                                    <div class="text-secondary mt-1">Kosongkan jika tidak ingin mengganti file.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary rounded-3 px-5">
                    <i class="bi bi-check2-circle me-1"></i>Simpan Dokumen
                </button>
                <a href="<?= base_url('admin/konten/download') ?>" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
