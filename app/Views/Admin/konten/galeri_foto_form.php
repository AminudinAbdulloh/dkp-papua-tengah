<?php

declare(strict_types=1);

use App\Models\GalleryPhotoModel;

/** @var array<string, mixed>|null $photo */
$p      = $photo ?? [];
$isEdit = $photo !== null;

// Slot gambar yang sudah tersimpan
$imgCols = ['image', 'image_2', 'image_3', 'image_4'];
$saved   = [];
foreach ($imgCols as $col) {
    $val = trim((string) ($p[$col] ?? ''));
    $saved[] = $val !== '' ? GalleryPhotoModel::publicImageUrl($val) : '';
}
?>

<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?><?= $isEdit ? 'Edit Galeri Foto' : 'Tambah Galeri Foto' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('admin/konten/galeri-foto') ?>">Galeri Foto</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Edit' : 'Tambah' ?></li>
        </ol>
    </nav>
    <h1 class="h3 fw-bold text-body mb-1"><?= $isEdit ? 'Edit Galeri Foto' : 'Tambah Galeri Foto' ?></h1>
    <p class="text-secondary mb-0">
        Setiap entri galeri dapat memuat hingga <strong>4 foto</strong>.
        <?= $isEdit ? 'Kosongkan input file jika tidak ingin mengganti foto yang sudah ada.' : '' ?>
    </p>
</div>

<?php
$errs = session()->getFlashdata('errors');
if (is_array($errs) && $errs !== []) { ?>
    <div class="alert alert-danger rounded-3">
        <ul class="mb-0 ps-3">
            <?php foreach ($errs as $err) { ?>
                <li><?= esc(is_string($err) ? $err : (string) $err) ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4 p-lg-5">
        <form method="post" action="<?= esc($formAction, 'attr') ?>" enctype="multipart/form-data" novalidate>
            <?= csrf_field() ?>

            <!-- Judul -->
            <div class="mb-4">
                <label for="title" class="form-label fw-semibold">Judul / Keterangan Album</label>
                <input type="text" class="form-control form-control-lg rounded-3"
                       id="title" name="title" required maxlength="255"
                       value="<?= esc(old('title', (string) ($p['title'] ?? ''))) ?>">
            </div>

            <?php if (! $isEdit) : ?>
                <!-- CREATE: Upload sekaligus 1-4 foto -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Foto (1–4 gambar)</label>
                    <div class="row g-3" id="upload-slots">
                        <?php for ($i = 1; $i <= 4; $i++) : ?>
                            <div class="col-12 col-sm-6">
                                <label class="form-label text-secondary small">Foto <?= $i ?> <?= $i === 1 ? '<span class="text-danger">*</span>' : '(opsional)' ?></label>
                                <input type="file" class="form-control rounded-3"
                                       name="gallery_images[]"
                                       accept="image/jpeg,image/png,image/webp,image/gif"
                                       <?= $i === 1 ? 'required' : '' ?>
                                       id="gallery_image_<?= $i ?>">
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="form-text mt-2">
                        <i class="bi bi-info-circle me-1"></i>JPG, PNG, WebP, atau GIF. Maksimal <strong>5MB per foto</strong>.
                        Foto pertama menjadi thumbnail utama.
                    </div>
                </div>

            <?php else : ?>
                <!-- EDIT: Slot individual per foto -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Foto (1–4 gambar)</label>
                    <p class="text-secondary small mb-3">Unggah file baru untuk mengganti. Centang "Hapus" untuk mengosongkan slot.</p>

                    <div class="row g-4">
                        <?php for ($i = 0; $i < 4; $i++) :
                            $slotNo  = $i + 1;
                            $fileKey = 'gallery_image_' . $slotNo;
                            $hasImg  = $saved[$i] !== '';
                        ?>
                            <div class="col-12 col-sm-6">
                                <div class="border rounded-3 p-3 bg-body-tertiary h-100">
                                    <div class="fw-semibold small mb-2">
                                        Foto <?= $slotNo ?>
                                        <?= $slotNo === 1 ? '<span class="badge bg-primary ms-1" style="font-size:0.7rem;">Utama</span>' : '' ?>
                                    </div>

                                    <?php if ($hasImg) : ?>
                                        <div class="mb-2">
                                            <img src="<?= esc($saved[$i], 'attr') ?>" alt=""
                                                 class="img-fluid rounded-2 border"
                                                 style="max-height: 140px; width: 100%; object-fit: cover;">
                                        </div>
                                    <?php else : ?>
                                        <div class="mb-2 text-secondary small fst-italic">Belum ada foto</div>
                                    <?php endif; ?>

                                    <input type="file" class="form-control form-control-sm rounded-3 mb-2"
                                           name="<?= $fileKey ?>"
                                           accept="image/jpeg,image/png,image/webp,image/gif"
                                           id="<?= $fileKey ?>">

                                    <?php if ($hasImg && $slotNo > 1) : ?>
                                        <div class="form-check mt-1">
                                            <input class="form-check-input" type="checkbox"
                                                   name="clear_image_<?= $slotNo ?>" value="1"
                                                   id="clear_<?= $slotNo ?>">
                                            <label class="form-check-label text-danger small" for="clear_<?= $slotNo ?>">
                                                Hapus foto ini
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="form-text mt-2">
                        <i class="bi bi-info-circle me-1"></i>JPG, PNG, WebP, atau GIF. Maksimal <strong>5MB per foto</strong>.
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tombol aksi -->
            <div class="d-flex flex-wrap gap-2 mt-2">
                <button type="submit" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-check2-circle me-1"></i>Simpan
                </button>
                <a class="btn btn-outline-secondary rounded-3" href="<?= base_url('admin/konten/galeri-foto') ?>">Kembali</a>
                <?php if ($isEdit) : ?>
                    <a class="btn btn-outline-secondary rounded-3"
                       href="<?= base_url('galeri/foto/' . (int) $p['id']) ?>"
                       target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Lihat di situs
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
