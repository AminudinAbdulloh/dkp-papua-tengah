<?php

declare(strict_types=1);

use App\Models\NewsArticleModel;

/** @var array<string, mixed>|null $article */
$a = $article ?? [];
$isEdit = $article !== null;
$statusOld = old('status', $isEdit ? ((int) ($a['is_published'] ?? 0) === 1 ? 'publish' : 'draft') : 'draft');
$currentImg = old('current_image', (string) ($a['image'] ?? ''));
?>

<?= $this->extend('layouts/template_admin') ?>

<?= $this->section('title') ?><?= $isEdit ? 'Edit Berita' : 'Tambah Berita' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-page-header mb-4">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('admin/konten/berita') ?>">Berita</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $isEdit ? 'Edit' : 'Tambah' ?></li>
        </ol>
    </nav>
    <h1 class="h3 fw-bold text-body mb-1"><?= $isEdit ? 'Edit Berita' : 'Tambah Berita Baru' ?></h1>
    <p class="text-secondary mb-0">
        Tanggal tampil di situs mengikuti waktu artikel pertama kali dibuat. Jumlah tayangan dihitung otomatis dari kunjungan pembaca.
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
        <form method="post" action="<?= esc($formAction, 'attr') ?>" enctype="multipart/form-data" data-berita-editor novalidate>
            <?= csrf_field() ?>

            <div class="mb-4">
                <label for="title" class="form-label fw-semibold">Judul</label>
                <input type="text" class="form-control form-control-lg rounded-3" id="title" name="title" required
                    maxlength="255" value="<?= esc(old('title', (string) ($a['title'] ?? ''))) ?>">
            </div>

            <div class="mb-4">
                <label for="excerpt" class="form-label fw-semibold">Ringkasan</label>
                <textarea class="form-control rounded-3" id="excerpt" name="excerpt" rows="3" maxlength="2000"
                    placeholder="Tampil di kartu berita dan daftar"><?= esc(old('excerpt', (string) ($a['excerpt'] ?? ''))) ?></textarea>
            </div>

            <div class="mb-4">
                <label for="featured_image" class="form-label fw-semibold">Gambar utama</label>
                <input type="file" class="form-control rounded-3" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif"
                    <?= $isEdit ? '' : 'required' ?>>
                <div class="form-text">Format JPG, PNG, WebP, atau GIF. Maksimal 5MB.<?= $isEdit ? ' Kosongkan jika tidak ingin mengganti gambar.' : '' ?></div>
                <?php if ($isEdit && $currentImg !== '') : ?>
                    <input type="hidden" name="current_image" value="<?= esc($currentImg, 'attr') ?>">
                    <div class="mt-3 border rounded-3 p-2 d-inline-block bg-body-tertiary">
                        <p class="small text-secondary mb-2">Gambar saat ini:</p>
                        <img src="<?= esc(NewsArticleModel::publicImageUrl($currentImg), 'attr') ?>" alt="" class="rounded-3" style="max-width: 280px; max-height: 180px; object-fit: cover;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="author" class="form-label fw-semibold">Penulis</label>
                <input type="text" class="form-control rounded-3" id="author" name="author" maxlength="120"
                    value="<?= esc(old('author', (string) ($a['author'] ?? ''))) ?>">
            </div>

            <div class="mb-4">
                <label for="content" class="form-label fw-semibold">Isi berita</label>
                <textarea class="form-control rounded-3 admin-richtext-source" id="content" name="content" rows="14"><?php
                    $body = old('content', (string) ($a['content'] ?? ''), false);
                    if ($body !== '' && ! is_html_string($body)) {
                        $body = plain_text_to_editor_html($body);
                    }
                    echo $body;
                ?></textarea>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_exclusive" name="is_exclusive" value="1" <?= old('is_exclusive', (string) ($a['is_exclusive'] ?? '')) === '1' || (!empty($a['is_exclusive']) && (int)$a['is_exclusive'] === 1) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold" for="is_exclusive">Jadikan Berita Eksklusif (Tampil di Hero Slider Halaman Berita)</label>
                </div>
            </div>

            <div class="mb-4">
                <label for="status" class="form-label fw-semibold">Status</label>
                <select class="form-select form-select-lg rounded-3" id="status" name="status" required>
                    <option value="draft" <?= $statusOld === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="publish" <?= $statusOld === 'publish' ? 'selected' : '' ?>>Terbit</option>
                </select>
                <div class="form-text">Draft tidak tampil di situs publik. Terbit akan menampilkan artikel di beranda dan halaman berita.</div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-check2-circle me-1"></i>Simpan
                </button>
                <a class="btn btn-outline-secondary rounded-3" href="<?= base_url('admin/konten/berita') ?>">Kembali</a>
                <?php if ($isEdit && (int) ($a['is_published'] ?? 0) === 1) : ?>
                    <a class="btn btn-outline-secondary rounded-3" href="<?= base_url('berita/' . (int) $a['id']) ?>"
                        target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Lihat di situs
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('admin/partials/tinymce_init') ?>
<script>
(function () {
    const form = document.querySelector('form[data-berita-editor]');
    if (form) {
        form.addEventListener('submit', function () {
            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }
        });
    }
})();
</script>
<?= $this->endSection() ?>
