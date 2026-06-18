<?php
$pageData = $pageData ?? [];
$breadcrumbs = $pageData['breadcrumbs'] ?? [];
$heroBackgroundImage = isset($pageData['backgroundImage']) ? trim((string) $pageData['backgroundImage']) : '';

if ($heroBackgroundImage === '') {
    // Tentukan kategori halaman berdasarkan path URI
    $uri = service('request')->getUri()->getPath();
    $uriPath = ltrim($uri, '/');
    
    // Bersihkan 'index.php/' di awal path jika ada (untuk web server tanpa mod_rewrite)
    if (str_starts_with($uriPath, 'index.php/')) {
        $uriPath = substr($uriPath, 10);
    }
    if ($uriPath === 'index.php') {
        $uriPath = '';
    }
    
    $model = model(\App\Models\SitePageModel::class);
    $setting = null;

    if (str_starts_with($uriPath, 'profil/')) {
        $setting = $model->findBySlug(\App\Models\SitePageModel::SLUG_PENGATURAN_HEADER_PROFIL);
        $heroBackgroundImage = (!empty($setting) && !empty($setting['body'])) ? base_url($setting['body']) : base_url('images/header_profil.png');
    } elseif (str_starts_with($uriPath, 'berita') || str_starts_with($uriPath, 'pengumuman')) {
        $setting = $model->findBySlug(\App\Models\SitePageModel::SLUG_PENGATURAN_HEADER_INFORMASI);
        $heroBackgroundImage = (!empty($setting) && !empty($setting['body'])) ? base_url($setting['body']) : base_url('images/header_informasi.png');
    } elseif (str_starts_with($uriPath, 'galeri/')) {
        $setting = $model->findBySlug(\App\Models\SitePageModel::SLUG_PENGATURAN_HEADER_GALERI);
        $heroBackgroundImage = (!empty($setting) && !empty($setting['body'])) ? base_url($setting['body']) : base_url('images/header_galeri.png');
    } elseif (
        str_starts_with($uriPath, 'publikasi') || 
        str_starts_with($uriPath, 'layanan/') || 
        str_starts_with($uriPath, 'informasi/') || 
        str_starts_with($uriPath, 'download')
    ) {
        $setting = $model->findBySlug(\App\Models\SitePageModel::SLUG_PENGATURAN_HEADER_PPID);
        $heroBackgroundImage = (!empty($setting) && !empty($setting['body'])) ? base_url($setting['body']) : base_url('images/header_ppid.png');
    }
}

$heroStyle = '';
if ($heroBackgroundImage !== '') {
    $heroStyle = ' style="background-image: linear-gradient(135deg, rgba(8, 47, 73, 0.62), rgba(8, 47, 73, 0.4)), url(\'' . esc($heroBackgroundImage, 'attr') . '\');"';
}
?>

<section class="public-page-hero<?= $heroBackgroundImage !== '' ? ' public-page-hero--with-image' : '' ?>" <?= $heroStyle ?>>
    <div class="hero-orbs">
        <div class="orb orb-cyan orb-top-right"></div>
        <div class="orb orb-blue orb-bottom-left"></div>
        <div class="orb orb-indigo orb-center"></div>
    </div>
    <div class="hero-grid-pattern"></div>
    <div class="hero-stripes"></div>

    <div class="container px-4 px-sm-5 px-lg-5 hero-content-wrap">

        <?php if (!empty($breadcrumbs)): ?>
            <div class="hero-breadcrumbs">
                <span class="crumb-home-icon" aria-hidden="true">
                    <i class="bi bi-house-door"></i>
                </span>
                <?php foreach ($breadcrumbs as $index => $crumb): ?>
                    <?php if ($index > 0): ?>
                        <span class="crumb-separator">/</span>
                    <?php endif ?>
                    <?php if (!empty($crumb['href']) && $index < count($breadcrumbs) - 1): ?>
                        <a href="<?= esc($crumb['href']) ?>"><?= esc($crumb['label']) ?></a>
                    <?php else: ?>
                        <span class="active"><?= esc($crumb['label']) ?></span>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php if (!empty($exclusiveNews)): ?>
            <div class="row align-items-center g-4 mt-1">
                <!-- Left Column: Title & Description -->
                <div class="col-lg-5">
                    <div class="hero-text-block text-start mb-0">
                        <div class="hero-title-line"></div>
                        <h1><?= esc($pageData['title'] ?? 'Berita') ?></h1>
                        <p class="mb-4"><?= esc($pageData['description'] ?? '') ?></p>
                        <div class="hero-dots" aria-hidden="true">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                </div>
                <!-- Right Column: Exclusive Glassmorphism Slider Card -->
                <div class="col-lg-7">
                    <div class="exc-card" id="exclusiveSlider">
                        <!-- Slides -->
                        <?php foreach ($exclusiveNews as $index => $item): ?>
                            <div class="exc-slide<?= $index === 0 ? ' exc-slide--active' : '' ?>" data-index="<?= $index ?>">
                                <!-- Left: Image -->
                                <a href="<?= base_url('berita/' . (int)$item['id']) ?>" class="exc-img-wrap text-decoration-none">
                                    <img src="<?= esc($item['image']) ?>" alt="<?= esc($item['title']) ?>" class="exc-img" loading="lazy">
                                </a>
                                <!-- Right: Info -->
                                <div class="exc-info">
                                    <span class="exc-badge">
                                        <i class="bi bi-star-fill me-1" style="font-size:0.65rem;"></i>Berita Eksklusif
                                    </span>
                                    <a href="<?= base_url('berita/' . (int)$item['id']) ?>" class="exc-title text-decoration-none">
                                        <?= esc($item['title']) ?>
                                    </a>
                                    <p class="exc-date">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?= esc($item['date']) ?>
                                    </p>
                                    <a href="<?= base_url('berita/' . (int)$item['id']) ?>" class="exc-read-btn">
                                        Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php if (count($exclusiveNews) > 1): ?>
                            <!-- Prev / Next Arrows -->
                            <button class="exc-arrow exc-arrow--prev" id="excPrev" aria-label="Sebelumnya">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="exc-arrow exc-arrow--next" id="excNext" aria-label="Berikutnya">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                            <!-- Pagination Dots -->
                            <div class="exc-dots" id="excDots">
                                <?php foreach ($exclusiveNews as $index => $item): ?>
                                    <button class="exc-dot<?= $index === 0 ? ' exc-dot--active' : '' ?>"
                                            data-to="<?= $index ?>" aria-label="Slide <?= $index + 1 ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <script>
            (function() {
                document.addEventListener('DOMContentLoaded', function () {
                    const slides  = document.querySelectorAll('#exclusiveSlider .exc-slide');
                    const dots    = document.querySelectorAll('#excDots .exc-dot');
                    const btnPrev = document.getElementById('excPrev');
                    const btnNext = document.getElementById('excNext');
                    if (!slides.length) return;

                    let current = 0;
                    let timer   = null;
                    const INTERVAL = 5000;

                    function goTo(n) {
                        slides[current].classList.remove('exc-slide--active');
                        if (dots[current]) dots[current].classList.remove('exc-dot--active');
                        current = (n + slides.length) % slides.length;
                        slides[current].classList.add('exc-slide--active');
                        if (dots[current]) dots[current].classList.add('exc-dot--active');
                    }

                    function startAuto() {
                        if (slides.length < 2) return;
                        timer = setInterval(() => goTo(current + 1), INTERVAL);
                    }

                    function resetAuto() { clearInterval(timer); startAuto(); }

                    if (btnPrev) btnPrev.addEventListener('click', () => { goTo(current - 1); resetAuto(); });
                    if (btnNext) btnNext.addEventListener('click', () => { goTo(current + 1); resetAuto(); });
                    dots.forEach(dot => {
                        dot.addEventListener('click', () => { goTo(+dot.dataset.to); resetAuto(); });
                    });

                    startAuto();
                });
            })();
            </script>
        <?php else: ?>
            <div class="hero-text-block">
                <div class="hero-title-line"></div>
                <h1><?= esc($pageData['title'] ?? 'Halaman Informasi') ?></h1>
                <p><?= esc($pageData['description'] ?? '') ?></p>
                <div class="hero-dots" aria-hidden="true">
                    <span></span><span></span><span></span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="hero-wave-divider" aria-hidden="true">
        <svg class="wave wave-back" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path
                d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z">
            </path>
        </svg>
        <svg class="wave wave-front" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path
                d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z">
            </path>
        </svg>
    </div>
</section>