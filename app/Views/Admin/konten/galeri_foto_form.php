<?php

declare(strict_types=1);

use App\Models\GalleryPhotoModel;

/** @var array<string, mixed>|null $photo */
$p = $photo ?? [];
$isEdit = $photo !== null;
$currentImg = old('current_image', (string) ($p['image'] ?? ''));
?>

<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?><?= $isEdit ? 'Edit Foto Galeri' : 'Tambah Foto Galeri' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('admin/konten/galeri-foto') ?>">Galeri Foto</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Edit' : 'Tambah' ?></li>
        </ol>
    </nav>
    <h1 class="h3 fw-bold text-body mb-1"><?= $isEdit ? 'Edit Foto Galeri' : 'Tambah Foto Galeri' ?></h1>
    <p class="text-secondary mb-0">
        Tanggal di situs mengikuti waktu unggah pertama (dibuat). Gambar dari URL eksternal (misalnya data lama) tetap didukung.
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

            <div class="mb-4">
                <label for="title" class="form-label fw-semibold">Judul / keterangan</label>
                <input type="text" class="form-control form-control-lg rounded-3" id="title" name="title"
                    <?= $isEdit ? 'required' : '' ?>
                    maxlength="255" value="<?= esc(old('title', (string) ($p['title'] ?? ''))) ?>">
                <?php if (! $isEdit) : ?>
                    <div class="form-text">Opsional — jika dikosongkan, judul otomatis diambil dari nama file. Saat upload beberapa foto, judul yang sama diberi nomor urut (misal: <em>Kegiatan 2</em>, <em>Kegiatan 3</em>).</div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="gallery_image" class="form-label fw-semibold">File gambar</label>
                <?php if ($isEdit) : ?>
                    <input type="file" class="form-control rounded-3" id="gallery_image" name="gallery_image"
                        accept="image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif">
                    <div class="form-text">JPG, PNG, WebP, atau GIF. Maksimal 5MB. Kosongkan bila hanya mengubah judul.</div>
                <?php else : ?>
                    <input type="file" class="form-control rounded-3" id="gallery_image" name="gallery_image[]"
                        accept="image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif"
                        multiple required>
                    <div class="form-text">
                        <i class="bi bi-images me-1"></i>Bisa pilih <strong>lebih dari 1 gambar</strong> sekaligus.
                        JPG, PNG, WebP, atau GIF. Maksimal <strong>5MB per file</strong>.
                    </div>
                <?php endif; ?>
                <?php if ($isEdit && $currentImg !== '') : ?>
                    <input type="hidden" name="current_image" value="<?= esc($currentImg, 'attr') ?>">
                    <div class="mt-3 border rounded-3 p-2 d-inline-block bg-body-tertiary">
                        <p class="small text-secondary mb-2">Gambar saat ini:</p>
                        <img src="<?= esc(GalleryPhotoModel::publicImageUrl($currentImg), 'attr') ?>" alt="" class="rounded-3" style="max-width: 320px; max-height: 220px; object-fit: contain;">
                    </div>
                <?php endif; ?>
            </div>


            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-check2-circle me-1"></i>Simpan
                </button>
                <a class="btn btn-outline-secondary rounded-3" href="<?= base_url('admin/konten/galeri-foto') ?>">Kembali</a>
                <?php if ($isEdit) : ?>
                    <a class="btn btn-outline-secondary rounded-3" href="<?= base_url('galeri/foto/' . (int) $p['id']) ?>"
                        target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Lihat di situs
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
