<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal - Dinas Kelautan dan Perikanan Provinsi Papua Tengah</title>
    <meta name="description" content="Portal resmi Dinas Kelautan dan Perikanan Provinsi Papua Tengah.">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/theme-tokens.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/portal.css') ?>">
</head>
<body>

<main class="portal-hero">
    <!-- Background -->
    <img src="<?= base_url('images/bg_portal.jpeg') ?>" alt="" class="portal-hero-bg" aria-hidden="true">
    <div class="portal-hero-overlay" aria-hidden="true"></div>

    <div class="portal-content">

        <!-- Logo atas (Provinsi Papua Tengah) -->
        <div class="portal-top-logo">
            <img src="<?= base_url('images/logo_prov_papua_tengah.png') ?>" alt="Logo Provinsi Papua Tengah">
        </div>

        <!-- Judul -->
        <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
            <div class="portal-title">DINAS KELAUTAN DAN PERIKANAN</div>
            <div class="portal-subtitle">Provinsi Papua Tengah</div>
        </div>

        <!-- Grid ikon -->
        <?php
        // Bangun map menu dari menuNavigasi berdasarkan nama
        $menuNavigasi = $menuNavigasi ?? [];
        $menuMap = [];
        foreach ($menuNavigasi as $m) {
            $menuMap[strtolower($m['nama'])] = $m;
            // Map first-level submenus to allow them to be top-level dropdowns (e.g. Galeri, Layanan)
            if (!empty($m['submenu'])) {
                foreach ($m['submenu'] as $sm) {
                    $menuMap[strtolower($sm['nama'])] = $sm;
                }
            }
        }

        $popupMenus = [
            'profil'    => ['title' => 'Profil',    'icon' => 'bi-building',         'menu' => $menuMap['profil']    ?? null],
            'informasi' => ['title' => 'Informasi', 'icon' => 'bi-info-circle',      'menu' => $menuMap['informasi'] ?? null],
            'publikasi' => ['title' => 'Publikasi', 'icon' => 'bi-journal-richtext',  'menu' => $menuMap['publikasi'] ?? null],
            'galeri'    => ['title' => 'Galeri',    'icon' => 'bi-images',           'menu' => $menuMap['galeri']    ?? null],
            'layanan'   => ['title' => 'Layanan',   'icon' => 'bi-grid-3x3-gap',     'menu' => $menuMap['layanan']   ?? null],
            'informasi publik' => ['title' => 'Informasi Publik', 'icon' => 'bi-file-earmark-text', 'menu' => $menuMap['informasi publik'] ?? null],
        ];
        ?>
        <div class="portal-grid" role="navigation" aria-label="Menu Portal">

            <!-- 1. Kementerian Kelautan dan Perikanan -->
            <div class="portal-item portal-item-has-dropdown" tabindex="0" role="button" aria-haspopup="true"
                 title="Kementerian Kelautan dan Perikanan">
                <div class="portal-icon-wrap">
                    <img src="<?= base_url('images/logo_kkp.png') ?>" alt="Logo KKP">
                </div>
                <span class="portal-item-label">Kementerian Kelautan dan Perikanan</span>

                <div class="portal-dropdown">
                    <div class="portal-dropdown-header">
                        <img src="<?= base_url('images/logo_kkp.png') ?>" alt="" class="portal-dropdown-header-logo" aria-hidden="true">
                        <span>Kementerian Kelautan dan Perikanan</span>
                    </div>
                    <ul class="portal-dropdown-list">
                        <li>
                            <a href="https://portaldata.kkp.go.id/datainsight/kusuka" target="_blank" rel="noopener noreferrer">
                                <i class="bi bi-chevron-right"></i>
                                KUSUKA
                            </a>
                        </li>
                        <li>
                            <a href="https://portaldata.kkp.go.id/login" target="_blank" rel="noopener noreferrer">
                                <i class="bi bi-chevron-right"></i>
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="https://portaldata.kkp.go.id/register" target="_blank" rel="noopener noreferrer">
                                <i class="bi bi-chevron-right"></i>
                                Register
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 2. Provinsi Papua Tengah -->
            <a href="https://papuatengahprov.go.id/" target="_blank" rel="noopener noreferrer"
               class="portal-item" title="Provinsi Papua Tengah">
                <div class="portal-icon-wrap">
                    <img src="<?= base_url('images/logo_prov_papua_tengah.png') ?>" alt="Logo Papua Tengah">
                </div>
                <span class="portal-item-label">Provinsi Papua Tengah</span>
            </a>

            <!-- 3. Sibapatengah -->
            <a href="https://sibapatengah.id/" target="_blank" rel="noopener noreferrer"
               class="portal-item" title="Sibapatengah">
                <div class="portal-icon-wrap">
                    <img src="<?= base_url('images/logo_prov_papua_tengah.png') ?>" alt="Logo Sibapatengah">
                </div>
                <span class="portal-item-label">Sibapatengah</span>
            </a>

            <!-- Dropdown Menus -->
            <?php foreach ($popupMenus as $key => $popup) : ?>
            <div class="portal-item portal-item-has-dropdown" tabindex="0" role="button" aria-haspopup="true" title="<?= $popup['title'] ?>">
                <div class="portal-icon-wrap">
                    <i class="bi <?= $popup['icon'] ?>"></i>
                </div>
                <span class="portal-item-label"><?= $popup['title'] ?></span>

                <!-- Dropdown (Speech Box) -->
                <div class="portal-dropdown">
                    <div class="portal-dropdown-header">
                        <i class="bi <?= $popup['icon'] ?>"></i>
                        <span><?= $popup['title'] ?></span>
                    </div>
                    <ul class="portal-dropdown-list">
                        <?php 
                        $m = $popup['menu'];
                        if ($m !== null && !empty($m['submenu'])) : 
                            foreach ($m['submenu'] as $sub) : ?>
                                <li>
                                    <a href="<?= esc($sub['link']) ?>">
                                        <i class="bi bi-chevron-right"></i>
                                        <?= esc($sub['nama']) ?>
                                    </a>
                                </li>
                            <?php endforeach;
                        elseif ($m !== null) : ?>
                            <li><a href="<?= esc($m['link']) ?>"><i class="bi bi-chevron-right"></i><?= esc($m['nama']) ?></a></li>
                        <?php else : ?>
                            <li><span class="portal-dropdown-empty">Menu belum tersedia</span></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <?php endforeach; ?>

        </div>

        <!-- Tombol ke beranda -->
        <a href="<?= base_url('beranda') ?>" class="portal-btn">
            Menuju ke Beranda
        </a>

    </div>
</main>

<!-- Footer -->
<?php
$agency  = $footerData['agency']      ?? [];
$socials = $footerData['socialLinks'] ?? [];
$contacts = $agency['contacts']       ?? [];
// Petakan kontak berdasarkan ikon
$alamat  = '';
$email   = '';
$telepon = '';
foreach ($contacts as $c) {
    if (str_contains($c['icon'] ?? '', 'geo'))       $alamat  = $c['text'] ?? '';
    if (str_contains($c['icon'] ?? '', 'envelope'))  $email   = $c['text'] ?? '';
    if (str_contains($c['icon'] ?? '', 'telephone')) $telepon = $c['text'] ?? '';
}
$agencyName = $agency['name'] ?? 'Dinas Kelautan dan Perikanan Papua Tengah';
?>
<footer class="portal-footer">
    <div class="portal-footer-title"><?= esc(strtoupper($agencyName)) ?></div>
    <?php if ($alamat !== '') : ?>
        <div class="portal-footer-contact">
            <i class="bi bi-geo-alt me-1"></i> <?= esc($alamat) ?>
        </div>
    <?php endif; ?>

    <!-- Baris 1: Telepon + Email -->
    <div class="portal-footer-links">
        <?php if ($telepon !== '') : ?>
            <span class="portal-footer-link">
                <i class="bi bi-telephone"></i> <?= esc($telepon) ?>
            </span>
        <?php endif; ?>
        <?php if ($email !== '') : ?>
            <a href="mailto:<?= esc($email) ?>" class="portal-footer-link">
                <i class="bi bi-envelope"></i> <?= esc($email) ?>
            </a>
        <?php endif; ?>
    </div>

    <!-- Baris 2: Sosial media (hanya jika ada) -->
    <?php $hasSocials = array_filter($socials, fn($s) => ! empty($s['url']) && $s['url'] !== '#'); ?>
    <?php if ($hasSocials) : ?>
        <div class="portal-footer-socials">
            <?php foreach ($socials as $soc) : ?>
                <?php if (! empty($soc['url']) && $soc['url'] !== '#') : ?>
                    <a href="<?= esc($soc['url']) ?>" class="portal-footer-link"
                       target="_blank" rel="noopener noreferrer">
                        <i class="bi <?= esc($soc['icon']) ?>"></i> <?= esc($soc['label']) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</footer>

<script>
(function () {
    const items = document.querySelectorAll('.portal-item-has-dropdown');
    
    items.forEach(item => {
        item.addEventListener('click', function(e) {
            // If clicking a link inside the dropdown, don't toggle
            if (e.target.closest('a')) return;
            
            e.stopPropagation();
            
            const isActive = this.classList.contains('active');
            
            // Close others
            items.forEach(el => el.classList.remove('active'));
            
            if (!isActive) {
                this.classList.add('active');
            }
        });

        // Accessibility
        item.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.portal-item-has-dropdown')) {
            items.forEach(el => el.classList.remove('active'));
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            items.forEach(el => el.classList.remove('active'));
        }
    });
})();
</script>

</body>
</html>

