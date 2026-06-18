<?php

declare(strict_types=1);

use App\Models\HeroBannerModel;

/** @var array<string, mixed>|null $banner */
$b      = $banner ?? [];
$isEdit = $banner !== null;
$imgUrl = $isEdit ? HeroBannerModel::publicImageUrl((string) ($b['image'] ?? '')) : '';
?>

<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?><?= $isEdit ? 'Edit Banner Ucapan' : 'Tambah Banner Ucapan' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('admin/pengaturan-beranda') ?>">Pengaturan Beranda</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Edit Banner' : 'Tambah Banner' ?></li>
        </ol>
    </nav>
    <h1 class="h3 fw-bold text-body mb-1"><?= $isEdit ? 'Edit Banner Ucapan' : 'Tambah Banner Ucapan' ?></h1>
    <p class="text-secondary mb-0">
        Banner ucapan ditampilkan di hero beranda tanpa overlay gradient. Rekomendasi resolusi: 2400×1000px.
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
                <label for="title" class="form-label fw-semibold">Judul / Keterangan</label>
                <input type="text" class="form-control form-control-lg rounded-3"
                    id="title" name="title" required maxlength="255"
                    value="<?= esc(old('title', (string) ($b['title'] ?? ''))) ?>"
                    placeholder="Contoh: Selamat Hari Kemerdekaan">
                <div class="form-text">Digunakan sebagai teks alternatif gambar (alt).</div>
            </div>

            <div class="mb-4">
                <label for="banner_image" class="form-label fw-semibold">
                    Gambar Banner <?= $isEdit ? '' : '<span class="text-danger">*</span>' ?>
                </label>
                <?php if ($isEdit && $imgUrl !== '') : ?>
                    <input type="hidden" name="current_image" value="<?= esc((string) ($b['image'] ?? ''), 'attr') ?>">
                    <div class="mb-3">
                        <img src="<?= esc($imgUrl, 'attr') ?>" alt="<?= esc((string) ($b['title'] ?? '')) ?>"
                            class="img-fluid rounded-3 border shadow-sm" style="max-height: 220px; object-fit: cover;">
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control rounded-3" id="banner_image" name="banner_image"
                    accept="image/jpeg,image/png,image/jpg,image/webp" <?= $isEdit ? '' : 'required' ?>>
                <div class="form-text">Maks. 5MB. Format JPG, PNG, atau WebP. Rekomendasi resolusi: 2400×1000px.</div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label for="sort_order" class="form-label fw-semibold">Urutan Tampil</label>
                    <input type="number" class="form-control rounded-3" id="sort_order" name="sort_order" min="0"
                        value="<?= esc(old('sort_order', (string) ($b['sort_order'] ?? '0'))) ?>">
                    <div class="form-text">Angka lebih kecil tampil lebih dulu.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold d-block">Status</label>
                    <?php $activeVal = old('is_active', (string) ($b['is_active'] ?? '1')); ?>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                            <?= $activeVal === '1' || (int) $activeVal === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Aktif (tampilkan di hero beranda)</label>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-save me-1"></i><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Banner' ?>
                </button>
                <a href="<?= base_url('admin/pengaturan-beranda') ?>" class="btn btn-light border rounded-3 px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
