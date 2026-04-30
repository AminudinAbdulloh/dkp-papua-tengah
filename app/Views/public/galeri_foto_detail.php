<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?><?= esc($photo['title']) ?> - Galeri Foto<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/public-page.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/beranda.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$images   = $photo['images'] ?? [$photo['image']];
$images   = array_values(array_filter($images, static fn($v) => trim((string)$v) !== ''));
$imgCount = count($images);
?>
<div class="public-page-wrapper galeri-foto-detail-wrapper">
    <?= $this->include('public/partials/hero_header') ?>

    <section class="public-page-content">
        <div class="container px-sm-5 px-lg-0">
            <div class="content-card galeri-foto-detail-card">
                <div class="row g-4 g-lg-5">

                    <!-- Kiri: Foto utama + thumbnail strip -->
                    <div class="col-lg-8">
                        <article class="photo-detail-article">

                            <!-- Meta -->
                            <div class="photo-detail-meta mb-3">
                                <span><i class="bi bi-calendar-event me-1"></i><?= esc($photo['date']) ?></span>
                                <?php if ($imgCount > 1) : ?>
                                    <span class="ms-3">
                                        <i class="bi bi-images me-1"></i><?= $imgCount ?> foto
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Gambar utama -->
                            <div class="photo-detail-image">
                                <img src="<?= esc($images[0], 'attr') ?>"
                                     alt="<?= esc($photo['title']) ?>"
                                     id="photoMainImg"
                                     class="photo-main-img">
                            </div>

                            <!-- Thumbnail strip — hanya tampil jika > 1 foto -->
                            <?php if ($imgCount > 1) : ?>
                                <div class="photo-thumb-strip mt-3">
                                    <?php foreach ($images as $idx => $imgUrl) : ?>
                                        <button type="button"
                                                class="photo-thumb-btn<?= $idx === 0 ? ' active' : '' ?>"
                                                data-src="<?= esc($imgUrl, 'attr') ?>"
                                                aria-label="Foto <?= $idx + 1 ?>">
                                            <img src="<?= esc($imgUrl, 'attr') ?>"
                                                 alt="Foto <?= $idx + 1 ?>">
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Judul -->
                            <div class="photo-detail-caption mt-3">
                                <h2><?= esc($photo['title']) ?></h2>
                            </div>

                        </article>
                    </div>

                    <!-- Kanan: Foto lainnya -->
                    <div class="col-lg-4">
                        <aside class="photo-related-box">
                            <h3 class="photo-related-title">
                                <i class="bi bi-images me-2"></i>Foto Lainnya
                            </h3>
                            <div class="photo-related-list">
                                <?php foreach ($relatedPhotos as $relatedPhoto) : ?>
                                    <a href="<?= base_url('galeri/foto/' . (int) $relatedPhoto['id']) ?>"
                                       class="photo-related-item">
                                        <img src="<?= esc($relatedPhoto['image']) ?>"
                                             alt="<?= esc($relatedPhoto['title']) ?>">
                                        <div>
                                            <h4><?= esc($relatedPhoto['title']) ?></h4>
                                            <p class="mb-0">
                                                <i class="bi bi-calendar-event me-1"></i><?= esc($relatedPhoto['date']) ?>
                                            </p>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <a href="<?= base_url('galeri/foto') ?>" class="photo-related-all-link">
                                Kembali ke Galeri Foto <i class="bi bi-arrow-right"></i>
                            </a>
                        </aside>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->section('scripts') ?>
<script>
(function () {
    var mainImg   = document.getElementById('photoMainImg');
    var thumbBtns = document.querySelectorAll('.photo-thumb-btn');
    if (!mainImg || !thumbBtns.length) return;

    thumbBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (btn.classList.contains('active')) return;

            // Fade-out
            mainImg.style.opacity = '0';

            setTimeout(function () {
                mainImg.src = btn.dataset.src;
                mainImg.style.opacity = '1';
            }, 180);

            // Update active state
            thumbBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
        });
    });
})();
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>