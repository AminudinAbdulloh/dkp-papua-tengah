<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?: 'Admin' ?></title>
    <link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('bootstrap-icons/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/theme-tokens.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>

<body class="admin-app-body">
    <?php
    helper('url');
    $nav = $adminNav ?? 'dashboard';

    // Keys yang masuk dalam grup dropdown "Dokumen Informasi Publik"
    $dokInfoKeys = ['mod-ppid', 'tipe-publikasi', 'kategori-publikasi'];
    $dokInfoOpen = in_array($nav, $dokInfoKeys, true);

    $sidebarNav = static function (string $key, string $label, string $href, string $icon, bool $disabled = false) use ($nav): string {
        $active = $nav === $key ? 'active' : '';
        if ($disabled) {
            return '<span class="admin-sidebar-link admin-sidebar-link--muted ' . $active . '">'
                . '<i class="bi ' . esc($icon, 'attr') . '"></i>'
                . '<span>' . esc($label) . '</span>'
                . '<span class="admin-sidebar-badge">nanti</span></span>';
        }

        return '<a class="admin-sidebar-link ' . $active . '" href="' . esc($href, 'attr') . '">'
            . '<i class="bi ' . esc($icon, 'attr') . '"></i>'
            . '<span>' . esc($label) . '</span></a>';
    };
    ?>

    <header class="admin-topbar border-bottom bg-body shadow-sm">
        <div class="container-fluid px-3 px-lg-4 py-2 py-lg-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2 flex-grow-1 flex-lg-grow-0 min-w-0">
                <button class="btn btn-outline-secondary btn-sm rounded-3 d-lg-none flex-shrink-0" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#adminSidebarCanvas" aria-controls="adminSidebarCanvas"
                    aria-label="Buka menu">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <!-- Logo Papua Tengah -->
                    <img src="<?= base_url('images/logo_prov_papua_tengah.png') ?>"
                         alt="Logo Provinsi Papua Tengah"
                         style="height: 38px; width: auto; object-fit: contain; flex-shrink: 0;"
                         class="d-none d-sm-block">
                    <div class="min-w-0">
                        <span class="admin-brand-badge rounded-3 px-2 py-1 fw-semibold small flex-shrink-0 d-inline-block">Admin</span>
                        <span class="text-secondary small text-truncate d-none d-sm-inline ms-1">Dinas Kelautan dan Perikanan Papua Tengah</span>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 gap-sm-3 flex-shrink-0">
                <a class="btn btn-outline-primary btn-sm rounded-3 d-none d-sm-inline-flex align-items-center" href="<?= base_url('/') ?>"
                    target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Situs
                </a>
                <span class="text-secondary small text-truncate" style="max-width: 9rem;">
                    <i class="bi bi-person-circle me-1"></i><?= esc(session('admin_name') ?? 'Admin') ?>
                </span>
                <a class="btn btn-outline-secondary btn-sm rounded-3" href="<?= base_url('admin/logout') ?>">
                    <i class="bi bi-box-arrow-right me-sm-1"></i><span class="d-none d-sm-inline">Keluar</span>
                </a>
            </div>
        </div>
    </header>

    <div class="admin-shell d-lg-flex">

        <!-- ============ SIDEBAR DESKTOP ============ -->
        <aside class="admin-sidebar d-none d-lg-flex flex-column border-end bg-body flex-shrink-0">
            <nav class="admin-sidebar-nav flex-grow-1 py-3 px-2">

                <!-- Dashboard -->
                <?= $sidebarNav('dashboard', 'Dashboard', base_url('admin/dashboard'), 'bi-speedometer2') ?>

                <!-- Profil -->
                <p class="admin-sidebar-label px-3 mb-2 mt-4">Profil</p>
                <?= $sidebarNav('konten-sejarah',  'Sejarah',             base_url('admin/konten/sejarah'),   'bi-clock-history') ?>
                <?= $sidebarNav('konten-visi-misi','Visi & Misi',         base_url('admin/konten/visi-misi'), 'bi-bullseye') ?>
                <?= $sidebarNav('konten-tupoksi',  'Tupoksi',             base_url('admin/konten/tupoksi'),   'bi-list-check') ?>
                <?= $sidebarNav('konten-struktur', 'Struktur Organisasi', base_url('admin/konten/struktur'),  'bi-diagram-3') ?>
                <?= $sidebarNav('konten-pejabat',  'Profil Pejabat',      base_url('admin/konten/pejabat'),   'bi-person-vcard') ?>
                <?= $sidebarNav('konten-kontak',   'Alamat dan Kontak',   base_url('admin/konten/kontak'),    'bi-geo-alt') ?>

                <!-- Informasi -->
                <p class="admin-sidebar-label px-3 mb-2 mt-4">Informasi</p>
                <?= $sidebarNav('konten-berita',       'Berita',       base_url('admin/konten/berita'),       'bi-newspaper') ?>
                <?= $sidebarNav('pengumuman',           'Pengumuman',   base_url('admin/pengumuman'),          'bi-megaphone') ?>
                <?= $sidebarNav('konten-galeri-foto',  'Galeri Foto',  base_url('admin/konten/galeri-foto'),  'bi-images') ?>
                <?= $sidebarNav('konten-galeri-video', 'Galeri Video', base_url('admin/konten/galeri-video'), 'bi-camera-video') ?>

                <!-- Dropdown: Dokumen Informasi Publik -->
                <button type="button"
                    class="admin-sidebar-dropdown-toggle<?= $dokInfoOpen ? ' active open' : '' ?>"
                    id="ddToggleDokInfo"
                    aria-expanded="<?= $dokInfoOpen ? 'true' : 'false' ?>"
                    aria-controls="ddDokInfo">
                    <i class="bi bi-folder-symlink"></i>
                    <span>Dokumen Informasi Publik</span>
                    <i class="bi bi-chevron-right admin-sidebar-caret"></i>
                </button>
                <ul class="admin-sidebar-dropdown<?= $dokInfoOpen ? ' open' : '' ?>" id="ddDokInfo">
                    <li><?= $sidebarNav('mod-ppid',          'Informasi Publik',      base_url('admin/konten/informasi-publik'),  'bi-journal-text') ?></li>
                    <li><?= $sidebarNav('tipe-publikasi',    'Kategori Publikasi',    base_url('admin/konten/tipe-publikasi'),    'bi-folder') ?></li>
                    <li><?= $sidebarNav('kategori-publikasi','Sub-Kategori Publikasi',base_url('admin/konten/kategori-publikasi'),'bi-folder2-open') ?></li>
                </ul>

                <!-- PPID -->
                <p class="admin-sidebar-label px-3 mb-2 mt-4">PPID</p>
                <?= $sidebarNav('konten-alur-informasi',        'Alur Informasi',       base_url('admin/konten/alur-informasi'),        'bi-signpost-split') ?>
                <?= $sidebarNav('konten-permohonan-informasi',  'Permohonan Informasi', base_url('admin/konten/permohonan-informasi'),  'bi-envelope-paper') ?>
                <?= $sidebarNav('konten-keberatan-informasi',   'Keberatan Informasi',  base_url('admin/konten/keberatan-informasi'),   'bi-exclamation-triangle') ?>

                <!-- Pengaturan -->
                <p class="admin-sidebar-label px-3 mb-2 mt-4">Pengaturan</p>
                <?= $sidebarNav('pengaturan-beranda', 'Pengaturan Beranda', base_url('admin/pengaturan-beranda'), 'bi-house-gear') ?>
                <?= $sidebarNav('manajemen-user',     'Manajemen User',     base_url('admin/manajemen-user'),     'bi-person-gear') ?>

            </nav>
            <div class="admin-sidebar-footer border-top p-3 small text-secondary">
                Semua modul konten situs dan layanan PPID dapat dikelola di sini.
            </div>
        </aside>

        <!-- ============ SIDEBAR MOBILE (Offcanvas) ============ -->
        <div class="offcanvas offcanvas-start admin-offcanvas text-bg-light d-lg-none" tabindex="-1" id="adminSidebarCanvas"
            aria-labelledby="adminSidebarCanvasLabel">
            <div class="offcanvas-header border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <img src="<?= base_url('images/logo_prov_papua_tengah.png') ?>"
                         alt="Logo Papua Tengah"
                         style="height: 28px; width: auto; object-fit: contain;">
                    <h2 class="offcanvas-title h6 mb-0" id="adminSidebarCanvasLabel">Menu Admin</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
            </div>
            <div class="offcanvas-body p-0">
                <nav class="admin-sidebar-nav py-2 px-2">
                    <?= $sidebarNav('dashboard', 'Dashboard', base_url('admin/dashboard'), 'bi-speedometer2') ?>

                    <!-- Profil -->
                    <p class="admin-sidebar-label px-3 mb-2 mt-3">Profil</p>
                    <?= $sidebarNav('konten-sejarah',  'Sejarah',             base_url('admin/konten/sejarah'),   'bi-clock-history') ?>
                    <?= $sidebarNav('konten-visi-misi','Visi & Misi',         base_url('admin/konten/visi-misi'), 'bi-bullseye') ?>
                    <?= $sidebarNav('konten-tupoksi',  'Tupoksi',             base_url('admin/konten/tupoksi'),   'bi-list-check') ?>
                    <?= $sidebarNav('konten-struktur', 'Struktur Organisasi', base_url('admin/konten/struktur'),  'bi-diagram-3') ?>
                    <?= $sidebarNav('konten-pejabat',  'Profil Pejabat',      base_url('admin/konten/pejabat'),   'bi-person-vcard') ?>
                    <?= $sidebarNav('konten-kontak',   'Alamat dan Kontak',   base_url('admin/konten/kontak'),    'bi-geo-alt') ?>

                    <!-- Informasi -->
                    <p class="admin-sidebar-label px-3 mb-2 mt-3">Informasi</p>
                    <?= $sidebarNav('konten-berita',       'Berita',       base_url('admin/konten/berita'),       'bi-newspaper') ?>
                    <?= $sidebarNav('pengumuman',           'Pengumuman',   base_url('admin/pengumuman'),          'bi-megaphone') ?>
                    <?= $sidebarNav('konten-galeri-foto',  'Galeri Foto',  base_url('admin/konten/galeri-foto'),  'bi-images') ?>
                    <?= $sidebarNav('konten-galeri-video', 'Galeri Video', base_url('admin/konten/galeri-video'), 'bi-camera-video') ?>

                    <!-- Dropdown mobile -->
                    <button type="button"
                        class="admin-sidebar-dropdown-toggle<?= $dokInfoOpen ? ' active open' : '' ?>"
                        id="ddToggleDokInfoMobile"
                        aria-expanded="<?= $dokInfoOpen ? 'true' : 'false' ?>"
                        aria-controls="ddDokInfoMobile">
                        <i class="bi bi-folder-symlink"></i>
                        <span>Dokumen Informasi Publik</span>
                        <i class="bi bi-chevron-right admin-sidebar-caret"></i>
                    </button>
                    <ul class="admin-sidebar-dropdown<?= $dokInfoOpen ? ' open' : '' ?>" id="ddDokInfoMobile">
                        <li><?= $sidebarNav('mod-ppid',          'Informasi Publik',       base_url('admin/konten/informasi-publik'),  'bi-journal-text') ?></li>
                        <li><?= $sidebarNav('tipe-publikasi',    'Kategori Publikasi',     base_url('admin/konten/tipe-publikasi'),    'bi-folder') ?></li>
                        <li><?= $sidebarNav('kategori-publikasi','Sub-Kategori Publikasi', base_url('admin/konten/kategori-publikasi'),'bi-folder2-open') ?></li>
                    </ul>

                    <!-- PPID -->
                    <p class="admin-sidebar-label px-3 mb-2 mt-3">PPID</p>
                    <?= $sidebarNav('konten-alur-informasi',       'Alur Informasi',       base_url('admin/konten/alur-informasi'),       'bi-signpost-split') ?>
                    <?= $sidebarNav('konten-permohonan-informasi', 'Permohonan Informasi', base_url('admin/konten/permohonan-informasi'), 'bi-envelope-paper') ?>
                    <?= $sidebarNav('konten-keberatan-informasi',  'Keberatan Informasi',  base_url('admin/konten/keberatan-informasi'),  'bi-exclamation-triangle') ?>

                    <!-- Pengaturan -->
                    <p class="admin-sidebar-label px-3 mb-2 mt-3">Pengaturan</p>
                    <?= $sidebarNav('pengaturan-beranda', 'Pengaturan Beranda', base_url('admin/pengaturan-beranda'), 'bi-house-gear') ?>
                    <?= $sidebarNav('manajemen-user',     'Manajemen User',     base_url('admin/manajemen-user'),     'bi-person-gear') ?>
                </nav>
            </div>
        </div>

        <!-- ============ MAIN CONTENT ============ -->
        <main class="admin-main flex-grow-1 min-w-0 px-3 px-lg-4 py-4">
            <?php if ($flash = session()->getFlashdata('message')) : ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    <?= esc($flash) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>

            <?php if ($flash = session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                    <?= esc($flash) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <script src="<?= base_url('js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('js/bootstrap.min.js') ?>"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    (function () {
        // Sidebar dropdown toggle – works for both desktop and mobile
        function initDropdownToggle(toggleId, listId) {
            var toggle = document.getElementById(toggleId);
            var list   = document.getElementById(listId);
            if (!toggle || !list) return;
 
            toggle.addEventListener('click', function () {
                var isOpen = list.classList.contains('open');
                list.classList.toggle('open', !isOpen);
                toggle.classList.toggle('open', !isOpen);
                toggle.classList.toggle('active', !isOpen);
                toggle.setAttribute('aria-expanded', String(!isOpen));
            });
        }
 
        initDropdownToggle('ddToggleDokInfo',       'ddDokInfo');
        initDropdownToggle('ddToggleDokInfoMobile', 'ddDokInfoMobile');

        // Global confirmation handler for SweetAlert2
        window.confirmAction = function(options) {
            const defaults = {
                title: 'Apakah Anda yakin?',
                text: 'Tindakan ini tidak dapat dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1d4ed8', // primary color
                cancelButtonColor: '#64748b',  // secondary color
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            };
            
            const config = { ...defaults, ...options };
            
            return Swal.fire(config).then((result) => {
                if (result.isConfirmed && typeof config.callback === 'function') {
                    config.callback();
                }
            });
        };

        // Listen for form submissions with data-confirm attribute
        document.addEventListener('submit', function(e) {
            if (e.target && e.target.hasAttribute('data-confirm')) {
                const form = e.target;
                if (form.getAttribute('data-confirmed') === 'true') {
                    return; // Allow form submission
                }
                
                e.preventDefault();
                
                const message = form.getAttribute('data-confirm');
                window.confirmAction({
                    text: message,
                    callback: function() {
                        form.setAttribute('data-confirmed', 'true');
                        form.submit();
                    }
                });
            }
        });
    })();
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>
