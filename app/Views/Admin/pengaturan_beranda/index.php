<?php

declare(strict_types=1);

use App\Models\HeroBannerModel;
use App\Models\SitePageModel;

$heroSlideMode = $heroSlideMode ?? SitePageModel::HERO_SLIDE_MODE_BERITA;
?>

<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?>Pengaturan Beranda<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengaturan Beranda</li>
        </ol>
    </nav>
    <h1 class="h3 fw-bold text-body mb-1">Pengaturan Beranda</h1>
    <p class="text-secondary mb-0">Atur latar belakang hero, mode slide, dan banner ucapan halaman beranda.</p>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')) : ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
        <ul class="mb-0 ps-3">
            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                <li><?= esc(is_string($error) ? $error : (string) $error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="<?= base_url('admin/pengaturan-beranda/update') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h2 class="h5 fw-bold mb-0"><i class="bi bi-image me-2 text-primary"></i>Latar Belakang Hero Beranda</h2>
        </div>
        <div class="card-body p-4">
            <div class="mb-4 text-center">
                <p class="text-secondary fw-semibold mb-2">Latar Belakang Slide Utama Saat Ini</p>
                <?php if (! empty($setting['body'])) : ?>
                    <img src="<?= base_url($setting['body']) ?>" alt="Hero Background"
                        class="img-fluid rounded-3 border shadow-sm" style="max-height: 250px; object-fit: cover; width: 100%;">
                <?php else : ?>
                    <div class="bg-light border rounded-3 d-flex align-items-center justify-content-center text-secondary" style="height: 250px;">
                        <span><i class="bi bi-image fs-4 me-2"></i>Menggunakan gambar default</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mb-0">
                <label for="hero_bg" class="form-label fw-semibold">Ubah Latar Belakang (Opsional)</label>
                <input class="form-control rounded-3" type="file" id="hero_bg" name="hero_bg" accept="image/jpeg,image/png,image/jpg,image/webp">
                <div class="form-text">Maksimal 4MB. Rekomendasi resolusi: 2400×1000px.</div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
            <h2 class="h5 fw-bold mb-0"><i class="bi bi-sliders me-2 text-primary"></i>Mode Slide Hero Tambahan</h2>
        </div>
        <div class="card-body p-4">
            <p class="text-secondary mb-3">
                Slide pertama hero selalu menampilkan informasi dinas. Slide tambahan dapat menampilkan berita terbaru atau banner ucapan.
            </p>
            <div class="d-flex flex-column gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="hero_slide_mode" id="mode_berita"
                        value="<?= SitePageModel::HERO_SLIDE_MODE_BERITA ?>"
                        <?= $heroSlideMode === SitePageModel::HERO_SLIDE_MODE_BERITA ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="mode_berita">
                        Berita Terbaru
                        <span class="d-block small fw-normal text-secondary">Menampilkan 3 berita terbaru yang sudah diterbitkan.</span>
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="hero_slide_mode" id="mode_banner"
                        value="<?= SitePageModel::HERO_SLIDE_MODE_BANNER ?>"
                        <?= $heroSlideMode === SitePageModel::HERO_SLIDE_MODE_BANNER ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="mode_banner">
                        Banner Ucapan
                        <span class="d-block small fw-normal text-secondary">Menampilkan banner ucapan yang Anda kelola di bawah, tanpa overlay gradient.</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 text-end">
        <button type="submit" class="btn btn-primary rounded-3 px-4">
            <i class="bi bi-save me-1"></i>Simpan Pengaturan
        </button>
    </div>
</form>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h2 class="h5 fw-bold mb-0"><i class="bi bi-images me-2 text-primary"></i>Kelola Banner Ucapan</h2>
        <a href="<?= base_url('admin/pengaturan-beranda/banner-ucapan/tambah') ?>" class="btn btn-primary btn-sm rounded-3">
            <i class="bi bi-plus-lg me-1"></i>Tambah Banner
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Preview</th>
                        <th>Judul</th>
                        <th class="d-none d-md-table-cell">Urutan</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (($banners ?? []) === []) : ?>
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-secondary">
                                Belum ada banner ucapan.
                                <a href="<?= base_url('admin/pengaturan-beranda/banner-ucapan/tambah') ?>">Tambah banner pertama</a>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($banners as $row) : ?>
                            <?php
                            $imgUrl = HeroBannerModel::publicImageUrl((string) ($row['image'] ?? ''));
                            $active = (int) ($row['is_active'] ?? 0) === 1;
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <?php if ($imgUrl !== '') : ?>
                                        <img src="<?= esc($imgUrl, 'attr') ?>" alt="<?= esc((string) ($row['title'] ?? '')) ?>"
                                            class="rounded-2 border" style="width: 120px; height: 50px; object-fit: cover;">
                                    <?php else : ?>
                                        <span class="text-secondary small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-medium"><?= esc((string) ($row['title'] ?? '')) ?></span>
                                </td>
                                <td class="d-none d-md-table-cell text-secondary"><?= esc((string) ($row['sort_order'] ?? '0')) ?></td>
                                <td>
                                    <?php if ($active) : ?>
                                        <span class="badge text-bg-success rounded-pill">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge text-bg-secondary rounded-pill">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary rounded-3"
                                        href="<?= base_url('admin/pengaturan-beranda/banner-ucapan/' . (int) $row['id'] . '/edit') ?>">Edit</a>
                                    <form method="post" action="<?= base_url('admin/pengaturan-beranda/banner-ucapan/' . (int) $row['id'] . '/hapus') ?>"
                                        class="d-inline" data-confirm="Hapus banner ucapan ini?">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
