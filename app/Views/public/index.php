<?= $this->extend('layouts/template_public') ?>

<?= $this->section('title') ?>Beranda - Dinas Kelautan dan Perikanan Papua Tengah<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/beranda.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/beranda.js') ?>"></script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<?php
use App\Models\SitePageModel;

$defaultBg       = $heroBg ?? 'https://images.unsplash.com/photo-1689505630546-bebf6e52dce2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHw0fHxmaXNoZXJtYW4lMjBvY2VhbiUyMGluZG9uZXNpYXxlbnwxfHx8fDE3NzU4MzcwNjZ8MA&ixlib=rb-4.1.0&q=80&w=1080';
$slides          = $heroExtraSlides ?? ($heroSlides ?? []);
$heroSlideMode   = $heroSlideMode ?? SitePageModel::HERO_SLIDE_MODE_BERITA;
$isBannerMode    = $heroSlideMode === SitePageModel::HERO_SLIDE_MODE_BANNER;
$hasSlides       = ! empty($slides);
$totalSlides     = $hasSlides ? count($slides) + 1 : 1;
?>
<section class="hero-section hero-carousel-wrap" aria-label="Hero Beranda">
    <div id="heroCarousel" class="carousel slide carousel-fade h-100"
         data-bs-ride="carousel" data-bs-interval="5000">

        <div class="carousel-inner h-100">

            <!-- Slide 0: Utama Dinas -->
            <div class="carousel-item active h-100">
                <img src="<?= esc($defaultBg) ?>" alt="Perikanan Papua Tengah" class="hero-bg-img">
                <div class="hero-overlay"></div>
                <div class="hero-slide-content d-flex align-items-center h-100">
                    <div class="container px-4 px-lg-2">
                        <div class="row">
                            <div class="col-lg-8 hero-anim-el">
                                <div class="mb-4">
                                    <span class="badge-custom">Pemerintah Provinsi Papua Tengah</span>
                                </div>
                                <h1 class="display-4 fw-bold text-white mb-4">
                                    Dinas Kelautan dan Perikanan Provinsi Papua Tengah
                                </h1>
                                <p class="text-light mb-5 fs-5">
                                    Mengelola dan mengembangkan potensi perikanan dan kelautan untuk kesejahteraan masyarakat Papua Tengah
                                </p>
                                <a href="<?= base_url('profil/sejarah') ?>" class="btn btn-primary btn-lg px-4 py-3">
                                    <i class="bi bi-info-circle me-2"></i>Tentang Kami
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide tambahan: Berita Terbaru atau Banner Ucapan -->
            <?php foreach ($slides as $slide) : ?>
                <?php if ($isBannerMode) : ?>
                    <?php
                    $bImg   = ! empty($slide['image']) ? $slide['image'] : $defaultBg;
                    $bTitle = $slide['title'] ?? 'Banner Ucapan';
                    ?>
                    <div class="carousel-item h-100 hero-banner-slide">
                        <img src="<?= esc($bImg, 'attr') ?>" alt="<?= esc($bTitle) ?>" class="hero-bg-img">
                    </div>
                <?php else : ?>
                    <?php
                    $sImg     = ! empty($slide['image']) ? $slide['image'] : $defaultBg;
                    $sTitle   = $slide['title'] ?? '';
                    $sId      = (int) ($slide['id'] ?? 0);
                    $sExcerpt = $slide['excerpt'] ?? '';
                    ?>
                    <div class="carousel-item h-100">
                        <img src="<?= esc($sImg) ?>" alt="<?= esc($sTitle) ?>" class="hero-bg-img">
                        <div class="hero-overlay hero-overlay-news"></div>
                        <div class="hero-slide-content d-flex align-items-center h-100">
                            <div class="container px-4 px-lg-2">
                                <div class="row">
                                    <div class="col-lg-7 hero-anim-el">
                                        <div class="mb-3">
                                            <span class="badge-custom">
                                                <i class="bi bi-newspaper me-1"></i>Berita Terkini
                                            </span>
                                        </div>
                                        <h2 class="hero-news-title fw-bold text-white mb-3"><?= esc($sTitle) ?></h2>
                                        <?php if ($sExcerpt !== '') : ?>
                                            <p class="hero-news-excerpt text-light mb-4"><?= esc($sExcerpt) ?></p>
                                        <?php endif; ?>
                                        <?php if ($sId > 0) : ?>
                                            <a href="<?= base_url('berita/' . $sId) ?>" class="btn btn-primary btn-lg px-4 py-3">
                                                <i class="bi bi-arrow-right me-2"></i>Selengkapnya
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if ($hasSlides) : ?>
            <!-- Tombol panah -->
            <button class="carousel-control-prev hero-carousel-btn" type="button"
                    data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Slide sebelumnya">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next hero-carousel-btn" type="button"
                    data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Slide berikutnya">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>

            <!-- Indicator bar -->
            <div class="carousel-indicators hero-carousel-indicators">
                <?php for ($d = 0; $d < $totalSlides; $d++) : ?>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $d ?>"
                            <?= $d === 0 ? 'class="active" aria-current="true"' : '' ?>
                            aria-label="Slide <?= $d + 1 ?>"></button>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</section>




<!-- Berita Terkini -->
<section id="berita" class="news-section">
    <div class="container px-4 px-lg-2">
        <div class="text-center mb-5 mx-auto" style="max-width: 700px;">
            <h2 class="fw-bold display-6 mb-3 text-dark-white">Berita Terkini</h2>
            <p class="text-muted fs-5 mb-4">Informasi dan kegiatan terbaru Dinas Perikanan dan Kelautan</p>
            <a href="<?= base_url('berita') ?>" class="btn btn-outline-primary rounded-pill px-4">
                Lihat Semua Berita <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>

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
    </div>
</section>

<!-- Galeri Foto -->
<section id="galeri" class="gallery-section">
    <div class="container px-4 px-lg-2">
        <div class="text-center mb-5 mx-auto" style="max-width: 700px;">
            <h2 class="fw-bold display-6 mb-3 text-dark-white">Galeri Foto</h2>
            <p class="text-muted fs-5 mb-4">Dokumentasi kegiatan dan potensi perikanan Papua Tengah</p>
            <a href="<?= base_url('galeri/foto') ?>" class="btn btn-outline-primary rounded-pill px-4">
                Lihat Semua Foto <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-3 g-md-4">
            <?php foreach ($galleryPhotos as $photo): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="<?= base_url('galeri/foto/' . (int) $photo['id']) ?>" class="gallery-item">
                        <img src="<?= esc($photo['image']) ?>" alt="<?= esc($photo['title']) ?>" class="gallery-image">
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
    </div>
</section>

<!-- Video Terbaru -->
<section id="video" class="video-section">
    <div class="container px-4 px-lg-2">
        <div class="text-center mb-5 mx-auto" style="max-width: 700px;">
            <h2 class="fw-bold display-6 mb-3 text-dark-white">Video Terbaru</h2>
            <p class="text-muted fs-5 mb-4">Video dokumentasi dan profil perikanan Papua Tengah</p>
            <a href="<?= base_url('galeri/video') ?>" class="btn btn-outline-primary rounded-pill px-4">
                Lihat Semua Video <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4 g-lg-5">
            <?php foreach ($latestVideos as $video): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="#" class="video-card js-video-trigger" data-youtube-id="<?= esc($video['youtube_id']) ?>"
                        data-video-title="<?= esc($video['title']) ?>">
                        <div class="video-thumb-wrap">
                            <img src="https://img.youtube.com/vi/<?= esc($video['youtube_id']) ?>/hqdefault.jpg"
                                alt="<?= esc($video['title']) ?>" class="video-thumb">
                            <div class="video-overlay"></div>
                            <div class="video-play-center">
                                <div class="video-play-btn">
                                    <i class="bi bi-play-fill"></i>
                                </div>
                            </div>
                        </div>

                        <div class="video-meta">
                            <i class="bi bi-play-circle"></i>
                            <time><?= esc($video['date']) ?></time>
                        </div>
                        <h3 class="video-title"><?= esc($video['title']) ?></h3>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<?= $this->include('public/partials/video_player_modal') ?>

<?= $this->endSection() ?>