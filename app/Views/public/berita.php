<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?>Berita - Dinas Kelautan dan Perikanan Papua Tengah<?= $this->endSection() ?>

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
                <div class="row g-4">
                    <?php foreach ($newsList as $news): ?>
                        <div class="col-md-6 col-lg-4">
                            <article class="news-card">
                                <img src="<?= esc($news['image']) ?>" alt="<?= esc($news['title']) ?>" class="news-image">
                                <div class="news-content">
                                    <div class="d-flex align-items-center gap-2 news-meta mb-3">
                                        <i class="bi bi-file-earmark-text"></i>
                                        <time><?= esc($news['date']) ?></time>
                                    </div>
                                    <h3 class="fw-bold mb-3 news-title"><?= esc($news['title']) ?></h3>
                                    <p class="mb-4 news-excerpt"><?= esc($news['excerpt']) ?></p>
                                    <a href="<?= base_url('berita/' . (int) $news['id']) ?>"
                                        class="btn btn-primary mt-3 d-inline-flex align-items-center gap-2">
                                        <span>Baca Selengkapnya</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </article>
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