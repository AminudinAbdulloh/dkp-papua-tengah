<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?>Galeri Foto - Dinas Kelautan dan Perikanan Papua Tengah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= asset('css/public-page.css') ?>">
<link rel="stylesheet" href="<?= asset('css/beranda.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="public-page-wrapper">
    <?= $this->include('public/partials/hero_header') ?>

    <section class="public-page-content">
        <div class="container px-sm-5 px-lg-0">
            <div class="content-card">
                <div class="row g-3 g-md-4">
                    <?php foreach ($galleryPhotos as $photo): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="<?= base_url('galeri/foto/' . (int) $photo['id']) ?>" class="gallery-item">
                                <img src="<?= esc($photo['image']) ?>" alt="<?= esc($photo['title']) ?>"
                                    class="gallery-image">
                                <div class="gallery-overlay">
                                    <div class="gallery-caption">
                                        <div class="gallery-date"><?= esc($photo['date']) ?></div>
                                        <p class="gallery-title"><?= esc($photo['title']) ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach ?>
                </div>
                <?php if (isset($pager) && $pager !== null): ?>
                    <div class="mt-5 d-flex justify-content-center">
                        <?= $pager->links('public', 'bootstrap_pagination') ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>