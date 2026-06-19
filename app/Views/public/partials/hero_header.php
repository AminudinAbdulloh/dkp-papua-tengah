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
            <div class="hero-text-block text-start mb-0">
                <div class="hero-title-line"></div>
                <h1><?= esc($pageData['title'] ?? 'Berita') ?></h1>
                <?php if (!empty($pageData['description'])): ?>
                    <p class="mb-0"><?= esc($pageData['description']) ?></p>
                <?php endif; ?>
                <div class="hero-dots" aria-hidden="true">
                    <span></span><span></span><span></span>
                </div>
            </div>

            <div class="exc-carousel-wrap">
                <div class="exc-carousel" id="exclusiveCarousel" data-count="<?= count($exclusiveNews) ?>">
                    <div class="exc-viewport">
                        <div class="exc-track" id="excTrack">
                            <?php foreach ($exclusiveNews as $item): ?>
                                <article class="exc-item">
                                    <div class="exc-card">
                                        <a href="<?= base_url('berita/' . (int) $item['id']) ?>" class="exc-img-wrap text-decoration-none">
                                            <img src="<?= esc($item['image']) ?>" alt="<?= esc($item['title']) ?>" class="exc-img" loading="lazy">
                                        </a>
                                        <div class="exc-info">
                                            <span class="exc-badge">
                                                <i class="bi bi-star-fill me-1" style="font-size:0.65rem;"></i>Berita Eksklusif
                                            </span>
                                            <a href="<?= base_url('berita/' . (int) $item['id']) ?>" class="exc-title text-decoration-none">
                                                <?= esc($item['title']) ?>
                                            </a>
                                            <p class="exc-date">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?= esc($item['date']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if (count($exclusiveNews) > 1): ?>
                        <button class="exc-arrow exc-arrow--prev" id="excPrev" type="button" aria-label="Sebelumnya">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="exc-arrow exc-arrow--next" id="excNext" type="button" aria-label="Berikutnya">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                        <div class="exc-dots" id="excDots" role="tablist" aria-label="Navigasi berita eksklusif"></div>
                    <?php endif; ?>
                </div>
            </div>

            <script>
            (function() {
                document.addEventListener('DOMContentLoaded', function () {
                    const carousel = document.getElementById('exclusiveCarousel');
                    const track    = document.getElementById('excTrack');
                    const btnPrev  = document.getElementById('excPrev');
                    const btnNext  = document.getElementById('excNext');
                    const dotsWrap = document.getElementById('excDots');
                    if (!carousel || !track) return;

                    let timer         = null;
                    let logicalIndex  = 0;
                    let physicalIndex = 0;
                    let realCount     = 0;
                    let isAnimating   = false;
                    const INTERVAL    = 5000;

                    function visibleCount() {
                        if (window.innerWidth < 576) return 1;
                        if (window.innerWidth < 992) return 2;
                        return 3;
                    }

                    function originals() {
                        return Array.from(track.querySelectorAll('.exc-item:not(.exc-item--clone)'));
                    }

                    function removeClones() {
                        track.querySelectorAll('.exc-item--clone').forEach((el) => el.remove());
                    }

                    function buildClones() {
                        removeClones();
                        const items = originals();
                        realCount = items.length;
                        const vis = visibleCount();

                        if (realCount <= vis) return;

                        for (let i = realCount - vis; i < realCount; i++) {
                            const clone = items[i].cloneNode(true);
                            clone.classList.add('exc-item--clone');
                            clone.setAttribute('aria-hidden', 'true');
                            track.insertBefore(clone, track.firstChild);
                        }

                        for (let i = 0; i < vis; i++) {
                            const clone = items[i].cloneNode(true);
                            clone.classList.add('exc-item--clone');
                            clone.setAttribute('aria-hidden', 'true');
                            track.appendChild(clone);
                        }
                    }

                    function stepSize() {
                        const item = track.querySelector('.exc-item');
                        if (!item) return 0;
                        const styles = getComputedStyle(track);
                        const gap = parseFloat(styles.columnGap || styles.gap || '0') || 0;
                        return item.offsetWidth + gap;
                    }

                    function translateTo(index, animate) {
                        if (!animate) track.style.transition = 'none';
                        track.style.transform = 'translateX(-' + (index * stepSize()) + 'px)';
                        if (!animate) {
                            track.offsetHeight;
                            track.style.transition = '';
                        }
                    }

                    function syncDots() {
                        if (!dotsWrap) return;
                        dotsWrap.querySelectorAll('.exc-dot').forEach((dot, idx) => {
                            dot.classList.toggle('exc-dot--active', idx === logicalIndex);
                        });
                    }

                    function renderDots() {
                        if (!dotsWrap) return;
                        dotsWrap.innerHTML = '';
                        for (let i = 0; i < realCount; i++) {
                            const dot = document.createElement('button');
                            dot.type = 'button';
                            dot.className = 'exc-dot' + (i === logicalIndex ? ' exc-dot--active' : '');
                            dot.setAttribute('aria-label', 'Slide ' + (i + 1));
                            dot.addEventListener('click', () => { goToLogical(i); resetAuto(); });
                            dotsWrap.appendChild(dot);
                        }
                    }

                    function updateArrows() {
                        const showNav = realCount > visibleCount();
                        if (btnPrev) btnPrev.style.display = showNav ? '' : 'none';
                        if (btnNext) btnNext.style.display = showNav ? '' : 'none';
                        if (dotsWrap) dotsWrap.style.display = showNav ? '' : 'none';
                    }

                    function goToLogical(index, animate) {
                        if (realCount <= visibleCount()) return;
                        logicalIndex = ((index % realCount) + realCount) % realCount;
                        physicalIndex = visibleCount() + logicalIndex;
                        translateTo(physicalIndex, animate !== false);
                        syncDots();
                    }

                    function goNext() {
                        if (realCount <= visibleCount() || isAnimating) return;

                        if (logicalIndex >= realCount - 1) {
                            isAnimating = true;
                            physicalIndex++;
                            logicalIndex = 0;
                            translateTo(physicalIndex, true);
                            track.addEventListener('transitionend', onWrapForward, { once: true });
                        } else {
                            logicalIndex++;
                            physicalIndex++;
                            translateTo(physicalIndex, true);
                            syncDots();
                        }
                    }

                    function onWrapForward(e) {
                        if (e.target !== track || e.propertyName !== 'transform') return;
                        isAnimating = false;
                        physicalIndex = visibleCount();
                        translateTo(physicalIndex, false);
                        syncDots();
                    }

                    function goPrev() {
                        if (realCount <= visibleCount() || isAnimating) return;

                        if (logicalIndex <= 0) {
                            logicalIndex = realCount - 1;
                            physicalIndex = visibleCount() - 1;
                            translateTo(physicalIndex, false);
                            syncDots();
                        } else {
                            logicalIndex--;
                            physicalIndex--;
                            translateTo(physicalIndex, true);
                            syncDots();
                        }
                    }

                    function startAuto() {
                        if (realCount <= visibleCount()) return;
                        timer = setInterval(goNext, INTERVAL);
                    }

                    function resetAuto() {
                        clearInterval(timer);
                        startAuto();
                    }

                    function refresh() {
                        buildClones();
                        logicalIndex = 0;
                        physicalIndex = visibleCount();
                        renderDots();
                        updateArrows();
                        if (realCount <= visibleCount()) {
                            translateTo(0, false);
                        } else {
                            translateTo(physicalIndex, false);
                            syncDots();
                        }
                    }

                    if (btnPrev) btnPrev.addEventListener('click', () => { goPrev(); resetAuto(); });
                    if (btnNext) btnNext.addEventListener('click', () => { goNext(); resetAuto(); });

                    window.addEventListener('resize', refresh);
                    refresh();
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