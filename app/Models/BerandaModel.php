<?php

namespace App\Models;

use Config\Database;

/**
 * BerandaModel
 *
 * Menyediakan data statis untuk halaman beranda.
 * Nantinya dapat diganti dengan query database.
 */
class BerandaModel
{
    /**
     * Data menu navigasi utama untuk navbar public.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPublicNavigationMenu(): array
    {
        $pubTypes = [];
        try {
            if (\Config\Database::connect()->tableExists('publication_types')) {
                $pubTypes = model(\App\Models\PublicationTypeModel::class)->orderBy('sort_order', 'ASC')->findAll();
            }
        } catch (\Throwable $e) {}

        $publikasiSubmenu = [];
        foreach ($pubTypes as $type) {
            $publikasiSubmenu[] = [
                'nama' => $type['name'],
                'link' => base_url('publikasi/' . $type['slug']),
            ];
        }

        return [
            [
                'nama' => 'Beranda',
                'link' => base_url('/beranda'),
                'aktif' => 'home',
            ],
            [
                'nama' => 'Profil',
                'link' => '#',
                'aktif' => 'profil',
                'submenu' => [
                    ['nama' => 'Sejarah', 'link' => base_url('profil/sejarah')],
                    ['nama' => 'Visi dan Misi', 'link' => base_url('profil/visi-misi')],
                    ['nama' => 'Tugas Pokok dan Fungsi', 'link' => base_url('profil/tupoksi')],
                    ['nama' => 'Struktur Organisasi', 'link' => base_url('profil/struktur')],
                    ['nama' => 'Profil Pejabat Struktural', 'link' => base_url('profil/pejabat')],
                    ['nama' => 'Alamat dan Kontak', 'link' => base_url('profil/kontak')],
                ],
            ],
            [
                'nama' => 'Informasi',
                'link' => '#',
                'aktif' => 'informasi',
                'submenu' => [
                    ['nama' => 'Berita', 'link' => base_url('berita')],
                    ['nama' => 'Pengumuman', 'link' => base_url('pengumuman')],
                    [
                        'nama' => 'Galeri',
                        'link' => '#',
                        'submenu' => [
                            ['nama' => 'Foto', 'link' => base_url('galeri/foto')],
                            ['nama' => 'Video', 'link' => base_url('galeri/video')],
                        ],
                    ],
                ],
            ],
            [
                'nama' => 'Publikasi',
                'link' => '#',
                'aktif' => 'publikasi',
                'submenu' => count($publikasiSubmenu) > 0 ? $publikasiSubmenu : [['nama' => 'Belum ada publikasi', 'link' => '#']],
            ],
            [
                'nama' => 'PPID',
                'link' => '#',
                'aktif' => 'ppid',
                'submenu' => [
                    [
                        'nama' => 'Layanan',
                        'link' => '#',
                        'submenu' => [
                            ['nama' => 'Alur Permohonan Informasi Publik', 'link' => base_url('layanan/alur-permohonan-informasi')],
                            ['nama' => 'Form Permohonan Informasi', 'link' => base_url('layanan/form-permohonan-informasi')],
                            ['nama' => 'Form Keberatan atas Permohonan Informasi', 'link' => base_url('layanan/form-keberatan-informasi')],
                        ],
                    ],
                    [
                        'nama' => 'Informasi Publik',
                        'link' => '#',
                        'submenu' => [
                            ['nama' => 'Daftar Informasi Publik', 'link' => base_url('publikasi/pelaporan?sub=daftar-informasi-publik')],
                            ['nama' => 'Informasi Berkala', 'link' => base_url('informasi/informasi-berkala')],
                            ['nama' => 'Informasi Serta Merta', 'link' => base_url('informasi/informasi-serta-merta')],
                            ['nama' => 'Informasi Setiap Saat', 'link' => base_url('informasi/informasi-setiap-saat')],
                            ['nama' => 'Informasi yang Dikecualikan', 'link' => base_url('informasi/informasi-dikecualikan')],
                        ],
                    ],
                ],
            ],
            [
                'nama' => 'Download',
                'link' => base_url('download'),
                'aktif' => 'download',
            ],

        ];
    }

    /**
     * Data konten footer halaman public.
     *
     * @return array<string, mixed>
     */
    public function getPublicFooterData(): array
    {
        $kontakModel = null;
        try {
            if (\Config\Database::connect()->tableExists('kontak')) {
                $kontakModel = model(KontakModel::class)->first();
            }
        } catch (\Throwable $e) {}

        $namaDinas = 'Dinas Kelautan dan Perikanan - Papua Tengah';
        $alamat = 'Sanoba, Distrik Nabire, Kabupaten Nabire, Papua Tengah 98816';
        $email = 'dislautkan@papua.go.id';
        $telepon = '(0123) 456789';
        $socials = [
            ['icon' => 'bi-instagram', 'label' => 'Instagram', 'url' => '#'],
            ['icon' => 'bi-youtube', 'label' => 'YouTube', 'url' => '#'],
        ];

        if ($kontakModel !== null) {
            $namaDinas = $kontakModel['nama_dinas'] ?: $namaDinas;
            $alamat = $kontakModel['alamat'] ?: $alamat;
            $email = $kontakModel['email'] ?: $email;
            $telepon = $kontakModel['telepon'] ?: $telepon;
            
            if (!empty($kontakModel['socials'])) {
                $decoded = json_decode($kontakModel['socials'], true);
                if (is_array($decoded) && count($decoded) > 0) {
                    $socials = [];
                    foreach ($decoded as $soc) {
                        $label = $soc['label'] ?? '';
                        $url = $soc['url'] ?? '#';
                        $icon = 'bi-link';
                        if (stripos($label, 'instagram') !== false) $icon = 'bi-instagram';
                        elseif (stripos($label, 'youtube') !== false) $icon = 'bi-youtube';
                        elseif (stripos($label, 'facebook') !== false) $icon = 'bi-facebook';
                        elseif (stripos($label, 'twitter') !== false || stripos($label, 'x') !== false) $icon = 'bi-twitter-x';
                        
                        $socials[] = ['icon' => $icon, 'label' => $label, 'url' => $url];
                    }
                }
            }
        }

        $stats = [
            ['icon' => 'bi-people', 'label' => 'Pengunjung Hari Ini', 'value' => '0', 'colorClass' => 'stat-color-blue'],
            ['icon' => 'bi-file-earmark-text', 'label' => 'Tayangan Hari Ini', 'value' => '0', 'colorClass' => 'stat-color-green'],
            ['icon' => 'bi-people-fill', 'label' => 'Pengunjung 7 Hari', 'value' => '0', 'colorClass' => 'stat-color-amber'],
            ['icon' => 'bi-bar-chart-line', 'label' => 'Total Pengunjung', 'value' => '0', 'colorClass' => 'stat-color-purple'],
            ['icon' => 'bi-eye', 'label' => 'Total Tayangan', 'value' => '0', 'colorClass' => 'stat-color-indigo'],
            ['icon' => 'bi-clock', 'label' => 'Terakhir Diperbarui', 'value' => date('d M Y'), 'colorClass' => 'stat-color-teal', 'small' => true],
        ];

        if (class_exists(\App\Models\VisitorModel::class) && \App\Models\VisitorModel::tableReady()) {
            $visitorModel = new \App\Models\VisitorModel();
            $stats[0]['value'] = number_format($visitorModel->getTodayVisitors(), 0, ',', '.');
            $stats[1]['value'] = number_format($visitorModel->getTodayViews(), 0, ',', '.');
            $stats[2]['value'] = number_format($visitorModel->get7DaysVisitors(), 0, ',', '.');
            $stats[3]['value'] = number_format($visitorModel->getTotalVisitors(), 0, ',', '.');
            $stats[4]['value'] = number_format($visitorModel->getTotalViews(), 0, ',', '.');
        }

        return [
            'agency' => [
                'icon' => 'bi-building',
                'name' => $namaDinas,
                'contacts' => [
                    ['icon' => 'bi-geo-alt', 'text' => $alamat],
                    ['icon' => 'bi-envelope', 'text' => $email],
                    ['icon' => 'bi-telephone', 'text' => $telepon],
                ],
            ],
            'informationLinks' => [
                ['label' => 'Berita Terbaru', 'url' => base_url('berita')],
                ['label' => 'Galeri Foto', 'url' => base_url('galeri/foto')],
                ['label' => 'Galeri Video', 'url' => base_url('galeri/video')],
                ['label' => 'Pengumuman', 'url' => base_url('pengumuman')],
            ],
            'socialLinks' => $socials,
            'stats' => $stats,
            'copyright' => '2026 Dinas Kelautan dan Perikanan Papua Tengah. All rights reserved.',
        ];
    }

    /**
     * Mengambil data halaman public non-beranda.
     *
     * @param string $path
     * @return array<string, mixed>|null
     */
    public function getPublicPageData(string $path): ?array
    {
        $pages = [
            'profil/sejarah' => [
                'title' => 'Sejarah Dinas',
                'description' => 'Perjalanan pembentukan dan pengembangan Dinas Kelautan dan Perikanan Provinsi Papua Tengah.',
            ],
            'profil/visi-misi' => [
                'title' => 'Visi dan Misi',
                'description' => 'Arah pembangunan sektor kelautan dan perikanan untuk mewujudkan kesejahteraan masyarakat pesisir.',
            ],
            'profil/tupoksi' => [
                'title' => 'Tugas Pokok dan Fungsi',
                'description' => 'Rincian tugas pokok serta fungsi organisasi dalam pelayanan publik bidang kelautan dan perikanan.',
            ],
            'profil/struktur' => [
                'title' => 'Struktur Organisasi',
                'description' => 'Susunan unit kerja dan alur koordinasi internal Dinas Kelautan dan Perikanan.',
            ],
            'profil/pejabat' => [
                'title' => 'Profil Pejabat Struktural',
                'description' => 'Informasi singkat pejabat struktural dan ruang lingkup tanggung jawabnya.',
            ],
            'profil/kontak' => [
                'title' => 'Alamat dan Kontak',
                'description' => 'Informasi alamat kantor, email, dan kanal komunikasi resmi layanan dinas.',
            ],
            'layanan/alur-permohonan-informasi' => [
                'title' => 'Alur Permohonan Informasi',
                'description' => 'Langkah-langkah pengajuan permohonan informasi publik secara mudah dan transparan.',
            ],
            'layanan/form-permohonan-informasi' => [
                'title' => 'Form Permohonan Informasi',
                'description' => 'Unduh dan isi formulir resmi pengajuan informasi publik melalui PPID Pelaksana.',
            ],
            'layanan/form-keberatan-informasi' => [
                'title' => 'Form Keberatan Informasi',
                'description' => 'Formulir pengajuan keberatan atas layanan informasi publik yang telah diberikan.',
            ],
            'informasi/informasi-dikecualikan' => [
                'title' => 'Informasi yang Dikecualikan',
                'description' => 'Informasi yang dikecualikan sesuai ketentuan peraturan perundang-undangan.',
            ],
            'informasi/informasi-berkala' => [
                'title' => 'Informasi Berkala',
                'description' => 'Informasi yang diumumkan secara berkala sebagai bentuk transparansi layanan.',
            ],
            'informasi/informasi-serta-merta' => [
                'title' => 'Informasi Serta Merta',
                'description' => 'Informasi penting bagi publik yang wajib diumumkan segera tanpa penundaan.',
            ],
            'informasi/informasi-setiap-saat' => [
                'title' => 'Informasi Setiap Saat',
                'description' => 'Informasi yang tersedia sewaktu-waktu untuk memenuhi kebutuhan publik.',
            ],
            'pengumuman' => [
                'title' => 'Pengumuman',
                'description' => 'Pengumuman resmi dan edaran terkait layanan Dinas Kelautan dan Perikanan Provinsi Papua Tengah.',
            ],

        ];

        if (!isset($pages[$path])) {
            return null;
        }

        $page = $pages[$path];
        $title = $page['title'];
        $description = $page['description'];
        $content = $this->buildPageContent($path, $title);

        $dbPageSlug = match ($path) {
            'profil/sejarah' => SitePageModel::SLUG_PROFIL_SEJARAH,
            'profil/visi-misi' => SitePageModel::SLUG_PROFIL_VISI_MISI,
            'profil/tupoksi' => SitePageModel::SLUG_PROFIL_TUPOKSI,
            'profil/struktur' => SitePageModel::SLUG_PROFIL_STRUKTUR,
            'profil/pejabat' => SitePageModel::SLUG_PROFIL_PEJABAT,
            'profil/kontak' => SitePageModel::SLUG_PROFIL_KONTAK,
            'layanan/alur-permohonan-informasi' => SitePageModel::SLUG_LAYANAN_ALUR_INFORMASI,
            default => null,
        };

        if ($dbPageSlug !== null) {
            $dbPage = model(SitePageModel::class)->findBySlug($dbPageSlug);
            if ($dbPage !== null) {
                if (trim((string) ($dbPage['title'] ?? '')) !== '') {
                    $title = (string) $dbPage['title'];
                }
                if (trim((string) ($dbPage['description'] ?? '')) !== '') {
                    $description = (string) $dbPage['description'];
                }
                if (trim((string) ($dbPage['body'] ?? '')) !== '') {
                    $content = (string) $dbPage['body'];
                }
            }
        }

        return [
            'path' => $path,
            'title' => $title,
            'description' => $description,
            'breadcrumbs' => $this->buildBreadcrumbs($path, $title),
            'content' => $content,
        ];
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function buildBreadcrumbs(string $path, string $title): array
    {
        $segments = explode('/', $path);
        $breadcrumbs = [
            ['label' => 'Beranda', 'href' => base_url('/beranda')],
        ];

        if (count($segments) === 2) {
            $breadcrumbs[] = ['label' => ucfirst($segments[0]), 'href' => null];
        }

        $breadcrumbs[] = ['label' => $title, 'href' => null];

        return $breadcrumbs;
    }

    private function buildPageContent(string $path, string $title): string
    {
        return "Halaman {$title} disusun sebagai media informasi resmi Dinas Kelautan dan Perikanan Provinsi Papua Tengah.\n\n"
            . "Konten pada halaman ini akan diperbarui secara berkala sesuai kebijakan organisasi, perkembangan program, dan kebutuhan layanan publik.\n\n"
            . "Untuk informasi lebih rinci terkait {$path}, masyarakat dapat menghubungi admin melalui kanal komunikasi resmi yang tersedia pada halaman kontak.";
    }

    /**
     * Daftar layanan utama yang ditampilkan di beranda.
     *
     * @return array<int, array<string, string>>
     */
    public function getServices(): array
    {
        return [
            [
                'icon' => 'bi-clipboard-check',
                'title' => 'Alur Permohonan Informasi Publik',
                'description' => 'Panduan dan prosedur pengajuan permohonan informasi publik',
                'link' => 'layanan/alur-permohonan-informasi',
            ],
            [
                'icon' => 'bi-file-earmark-text',
                'title' => 'Form Permohonan Informasi',
                'description' => 'Formulir pengajuan permohonan informasi publik secara online',
                'link' => 'layanan/form-permohonan-informasi',
            ],
            [
                'icon' => 'bi-shield-exclamation',
                'title' => 'Form Keberatan Informasi',
                'description' => 'Formulir pengajuan keberatan atas permohonan informasi',
                'link' => 'layanan/form-keberatan-informasi',
            ],
            [
                'icon' => 'bi-folder2-open',
                'title' => 'Daftar Informasi Publik',
                'description' => 'Katalog dan daftar informasi publik yang tersedia',
                'link' => 'publikasi/daftar-informasi-publik',
            ],
            [
                'icon' => 'bi-lock',
                'title' => 'Daftar Informasi Dikecualikan',
                'description' => 'Informasi publik yang dikecualikan dari layanan informasi',
                'link' => 'informasi/informasi-dikecualikan',
            ],
            [
                'icon' => 'bi-clock-history',
                'title' => 'Informasi Setiap Saat',
                'description' => 'Informasi publik yang dapat diakses setiap saat',
                'link' => 'informasi/informasi-setiap-saat',
            ],
            [
                'icon' => 'bi-calendar-event',
                'title' => 'Informasi Berkala',
                'description' => 'Informasi publik yang disediakan secara berkala',
                'link' => 'informasi/informasi-berkala',
            ],
            [
                'icon' => 'bi-megaphone',
                'title' => 'Informasi Serta Merta',
                'description' => 'Informasi yang wajib diumumkan segera demi kepentingan publik',
                'link' => 'informasi/informasi-serta-merta',
            ],
        ];
    }

    /**
     * Daftar berita terkini untuk ditampilkan di beranda.
     *
     * @return array<int, array<string, int|string>>
     */
    public function getNewsList(int $limit = 3): array
    {
        if ($this->isNewsArticlesTablePresent()) {
            return model(NewsArticleModel::class)->getPublishedForPublic($limit);
        }

        return $this->getStaticNewsListFallback();
    }

    /**
     * Mengambil detail berita berdasarkan ID.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function getNewsDetail(int $id): ?array
    {
        if ($this->isNewsArticlesTablePresent()) {
            return model(NewsArticleModel::class)->getPublishedById($id);
        }

        foreach ($this->getStaticNewsListFallback() as $news) {
            if ((int) $news['id'] === $id) {
                return $news;
            }
        }

        return null;
    }

    /**
     * Mengambil daftar berita populer untuk sidebar.
     *
     * @param int|null $excludeId
     * @return array<int, array<string, mixed>>
     */
    public function getPopularNews(?int $excludeId = null): array
    {
        if ($this->isNewsArticlesTablePresent()) {
            return model(NewsArticleModel::class)->getPopularPublished($excludeId, 4);
        }

        $popular = $this->getStaticNewsListFallback();

        if ($excludeId !== null) {
            $popular = array_values(array_filter(
                $popular,
                static fn(array $news): bool => (int) $news['id'] !== $excludeId
            ));
        }

        usort($popular, static function (array $a, array $b): int {
            $viewsA = (int) preg_replace('/\D+/', '', (string) ($a['views'] ?? '0'));
            $viewsB = (int) preg_replace('/\D+/', '', (string) ($b['views'] ?? '0'));

            return $viewsB <=> $viewsA;
        });

        return array_slice($popular, 0, 4);
    }

    /**
     * Daftar foto galeri untuk ditampilkan di beranda.
     *
     * @return array<int, array<string, int|string>>
     */
    public function getGalleryPhotos(int $limit = 8): array
    {
        if ($this->isGalleryPhotosTablePresent()) {
            return model(GalleryPhotoModel::class)->getForPublic($limit);
        }

        return $this->getStaticGalleryPhotosFallback();
    }

    /**
     * Mengambil detail foto galeri berdasarkan ID.
     *
     * @param int $id
     * @return array<string, int|string>|null
     */
    public function getGalleryPhotoDetail(int $id): ?array
    {
        if ($this->isGalleryPhotosTablePresent()) {
            return model(GalleryPhotoModel::class)->getByIdForPublic($id);
        }

        foreach ($this->getStaticGalleryPhotosFallback() as $photo) {
            if ((int) $photo['id'] === $id) {
                return $photo;
            }
        }

        return null;
    }

    /**
     * Mengambil daftar foto terkait untuk sidebar/detail.
     *
     * @param int $excludeId
     * @param int $limit
     * @return array<int, array<string, int|string>>
     */
    public function getRelatedGalleryPhotos(int $excludeId, int $limit = 4): array
    {
        if ($this->isGalleryPhotosTablePresent()) {
            return model(GalleryPhotoModel::class)->getRelatedForPublic($excludeId, $limit);
        }

        $photos = array_values(array_filter(
            $this->getStaticGalleryPhotosFallback(),
            static fn(array $photo): bool => (int) $photo['id'] !== $excludeId
        ));

        return array_slice($photos, 0, $limit);
    }

    /**
     * Daftar video terbaru untuk ditampilkan di beranda.
     *
     * @return array<int, array<string, int|string>>
     */
    public function getLatestVideos(int $limit = 6): array
    {
        if ($this->isGalleryVideosTablePresent()) {
            return model(GalleryVideoModel::class)->getForPublic($limit);
        }

        return $this->getStaticGalleryVideosFallback();
    }

    private function isGalleryVideosTablePresent(): bool
    {
        try {
            return Database::connect()->tableExists('gallery_videos');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Data demo bila tabel galeri video belum dimigrasikan.
     *
     * @return array<int, array<string, int|string>>
     */
    private function getStaticGalleryVideosFallback(): array
    {
        return [
            [
                'id' => 1,
                'youtube_id' => 'N4lNE4MBJG8',
                'youtube_url' => 'https://youtu.be/N4lNE4MBJG8?si=iJgIJ2zXTzmsrp52',
                'title' => 'Profil Nelayan Papua Tengah',
                'date' => '2 April 2026',
            ],
            [
                'id' => 2,
                'youtube_id' => 'B2pCPefPba4',
                'youtube_url' => 'https://youtu.be/B2pCPefPba4?si=VG4HgLFILNfCqzF4',
                'title' => 'Konservasi Terumbu Karang Teluk Cenderawasih',
                'date' => '25 Maret 2026',
            ],
            [
                'id' => 3,
                'youtube_id' => 'eQwIKZhxdzc',
                'youtube_url' => 'https://youtu.be/eQwIKZhxdzc?si=tDVQ6nNY13VfxBku',
                'title' => 'Penyerahan Bantuan Kapal Perikanan',
                'date' => '18 Maret 2026',
            ],
            [
                'id' => 4,
                'youtube_id' => 'l4i_zI69klU',
                'youtube_url' => 'https://youtu.be/l4i_zI69klU?si=6glMP5KJb75Lv-5w',
                'title' => 'Pelatihan Budidaya Ikan Modern',
                'date' => '10 Maret 2026',
            ],
            [
                'id' => 5,
                'youtube_id' => 'dndRLICiZbU',
                'youtube_url' => 'https://youtu.be/dndRLICiZbU?si=zrJSKTjBh7lhCOAX',
                'title' => 'Pembangunan Pelabuhan Perikanan Baru',
                'date' => '5 Maret 2026',
            ],
            [
                'id' => 6,
                'youtube_id' => 'PlZaWP064KA',
                'youtube_url' => 'https://youtu.be/PlZaWP064KA?si=1n7rU7_4F_JAv7l0',
                'title' => 'Eksplorasi Kekayaan Laut Papua Tengah',
                'date' => '1 Maret 2026',
            ],
        ];
    }

    private function isGalleryPhotosTablePresent(): bool
    {
        try {
            return Database::connect()->tableExists('gallery_photos');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Data demo bila tabel galeri foto belum dimigrasikan.
     *
     * @return array<int, array<string, int|string>>
     */
    private function getStaticGalleryPhotosFallback(): array
    {
        return [
            [
                'id' => 1,
                'image' => 'https://images.unsplash.com/photo-1660278988532-d55143363abb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxmaXNoZXJtYW4lMjBvY2VhbiUyMGluZG9uZXNpYXxlbnwxfHx8fDE3NzU4MzcwNjZ8MA&ixlib=rb-4.1.0&q=80&w=1080',
                'title' => 'Armada Perikanan Tradisional',
                'date' => '1 Januari 2026',
            ],
            [
                'id' => 2,
                'image' => 'https://images.unsplash.com/photo-1562656611-2b26567ccf19?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwyfHxmaXNoZXJtYW4lMjBvY2VhbiUyMGluZG9uZXNpYXxlbnwxfHx8fDE3NzU4MzcwNjZ8MA&ixlib=rb-4.1.0&q=80&w=1080',
                'title' => 'Nelayan Papua Tengah',
                'date' => '10 Januari 2026',
            ],
            [
                'id' => 3,
                'image' => 'https://images.unsplash.com/photo-1724257154172-b7dcef926dea?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHw0fHxjb3JhbCUyMHJlZWYlMjB1bmRlcndhdGVyJTIwcGFwdWF8ZW58MXx8fHwxNzc1ODM3MDY2fDA&ixlib=rb-4.1.0&q=80&w=1080',
                'title' => 'Terumbu Karang Teluk Cenderawasih',
                'date' => '20 Januari 2026',
            ],
            [
                'id' => 4,
                'image' => 'https://images.unsplash.com/photo-1601699006891-c27e05b161c9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHw1fHxmaXNoaW5nJTIwYm9hdCUyMGhhcmJvcnxlbnwxfHx8fDE3NzU4MzcwNjZ8MA&ixlib=rb-4.1.0&q=80&w=1080',
                'title' => 'Pelabuhan Perikanan Nabire',
                'date' => '30 Januari 2026',
            ],
            [
                'id' => 5,
                'image' => 'https://images.unsplash.com/photo-1724257496887-d5012cdc9400?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHw1fHxjb3JhbCUyMHJlZWYlMjB1bmRlcndhdGVyJTIwcGFwdWF8ZW58MXx8fHwxNzc1ODM3MDY2fDA&ixlib=rb-4.1.0&q=80&w=1080',
                'title' => 'Keanekaragaman Hayati Laut',
                'date' => '2 Februari 2026',
            ],
            [
                'id' => 6,
                'image' => 'https://images.unsplash.com/photo-1689505630546-bebf6e52dce2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHw0fHxmaXNoZXJtYW4lMjBvY2VhbiUyMGluZG9uZXNpYXxlbnwxfHx8fDE3NzU4MzcwNjZ8MA&ixlib=rb-4.1.0&q=80&w=1080',
                'title' => 'Aktivitas Penangkapan Ikan',
                'date' => '11 Februari 2026',
            ],
            [
                'id' => 7,
                'image' => 'https://images.unsplash.com/photo-1582965637751-2c8cc0c164ce?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwzfHxmaXNoZXJtYW4lMjBvY2VhbiUyMGluZG9uZXNpYXxlbnwxfHx8fDE3NzU4MzcwNjZ8MA&ixlib=rb-4.1.0&q=80&w=1080',
                'title' => 'Dermaga Perikanan',
                'date' => '21 Februari 2026',
            ],
            [
                'id' => 8,
                'image' => 'https://images.unsplash.com/photo-1630546221335-bfbbe63f5e0a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHw1fHxmaXNoZXJtYW4lMjBvY2VhbiUyMGluZG9uZXNpYXxlbnwxfHx8fDE3NzU4MzcwNjZ8MA&ixlib=rb-4.1.0&q=80&w=1080',
                'title' => 'Perjalanan Mencari Ikan',
                'date' => '3 Maret 2026',
            ],
        ];
    }

    private function isNewsArticlesTablePresent(): bool
    {
        try {
            return Database::connect()->tableExists('news_articles');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Data demo bila tabel berita belum dimigrasikan.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getStaticNewsListFallback(): array
    {
        return [
            [
                'id' => 1,
                'date' => '5 April 2026',
                'title' => 'Penyerahan Bantuan Alat Tangkap kepada 200 Nelayan',
                'excerpt' => 'Gubernur Papua Tengah menyerahkan bantuan alat tangkap modern kepada nelayan di Kabupaten Nabire Gubernur Papua Tengah menyerahkan bantuan alat tangkap modern kepada nelayan di Kabupaten Nabire Gubernur Papua Tengah menyerahkan bantuan alat tangkap modern kepada nelayan di Kabupaten Nabire Gubernur Papua Tengah menyerahkan bantuan alat tangkap modern kepada nelayan di Kabupaten Nabire v Gubernur Papua Tengah menyerahkan bantuan alat tangkap modern kepada nelayan di Kabupaten Nabire Gubernur Papua Tengah menyerahkan bantuan alat tangkap modern kepada nelayan di Kabupaten Nabire v Gubernur Papua Tengah menyerahkan bantuan alat tangkap modern kepada nelayan di Kabupaten Nabire',
                'image' => 'https://images.unsplash.com/photo-1660278988532-d55143363abb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxmaXNoZXJtYW4lMjBvY2VhbiUyMGluZG9uZXNpYXxlbnwxfHx8fDE3NzU4MzcwNjZ8MA&ixlib=rb-4.1.0&q=80&w=1080',
                'author' => 'Admin Dinas',
                'views' => '1,234',
                'content' => '<p>Nabire - Gubernur Papua Tengah menyerahkan bantuan alat tangkap modern kepada 200 nelayan di Kabupaten Nabire pada Jumat, 5 April 2026. Program ini merupakan bagian dari upaya pemerintah provinsi dalam meningkatkan produktivitas dan kesejahteraan nelayan.</p>
<p>Dalam sambutannya, Gubernur menyampaikan bahwa bantuan ini diharapkan dapat membantu nelayan meningkatkan hasil tangkapan mereka. "Alat tangkap modern ini dirancang untuk lebih efisien dan ramah lingkungan," ujar Gubernur.</p>
<h2>Bantuan yang Diberikan</h2>
<p>Bantuan yang diserahkan meliputi:</p>
<ul>
  <li>Jaring ikan modern dengan ukuran yang sesuai standar</li>
  <li>Pancing tuna longline</li>
  <li>Alat bantu penangkapan ikan</li>
  <li>GPS dan fish finder</li>
</ul>
<h2>Dampak Positif</h2>
<p>Para nelayan menyambut baik program ini. Menurut salah satu penerima bantuan, Bapak Johannes Kogoya, alat tangkap modern ini akan sangat membantu meningkatkan hasil tangkapan. "Kami sangat berterima kasih kepada Pemerintah Provinsi Papua Tengah atas perhatiannya terhadap kesejahteraan nelayan," ungkapnya.</p>
<p>Kepala Dinas Perikanan dan Kelautan Papua Tengah menambahkan bahwa program ini akan dilanjutkan ke kabupaten lain di Papua Tengah. "Tahun ini kami menargetkan 1.000 nelayan akan menerima bantuan alat tangkap," jelasnya.</p>
<h2>Pelatihan Pendampingan</h2>
<p>Selain bantuan alat tangkap, para nelayan juga akan mendapatkan pelatihan cara penggunaan alat dan teknik penangkapan ikan yang berkelanjutan. Pelatihan ini akan dilaksanakan selama 3 hari dengan didampingi oleh tim teknis dari dinas.</p>
<p>Program ini diharapkan dapat meningkatkan produksi perikanan tangkap di Papua Tengah serta meningkatkan pendapatan nelayan secara signifikan.</p>',
            ],
            [
                'id' => 2,
                'date' => '28 Maret 2026',
                'title' => 'Pelatihan Budidaya Ikan Nila untuk Kelompok Tani',
                'excerpt' => 'Dinas menggelar pelatihan budidaya ikan nila sistem bioflok untuk 50 kelompok pembudidaya',
                'image' => 'https://images.unsplash.com/photo-1562656611-2b26567ccf19?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwyfHxmaXNoZXJtYW4lMjBvY2VhbiUyMGluZG9uZXNpYXxlbnwxfHx8fDE3NzU4MzcwNjZ8MA&ixlib=rb-4.1.0&q=80&w=1080',
                'author' => 'Bidang Budidaya',
                'views' => '2,145',
                'content' => '<p>Dinas Kelautan dan Perikanan menggelar pelatihan budidaya ikan nila sistem bioflok bagi 50 kelompok pembudidaya di Nabire. Kegiatan ini bertujuan meningkatkan kapasitas teknis masyarakat dalam budidaya berkelanjutan.</p>
<p>Materi pelatihan meliputi manajemen kualitas air, formulasi pakan, dan pengendalian penyakit ikan. Peserta juga mendapatkan praktik langsung untuk penerapan teknologi bioflok di kolam budidaya.</p>',
            ],
            [
                'id' => 3,
                'date' => '15 Maret 2026',
                'title' => 'Monitoring Kesehatan Terumbu Karang di Teluk Cenderawasih',
                'excerpt' => 'Tim survei melakukan monitoring kondisi ekosistem terumbu karang di kawasan konservasi',
                'image' => 'https://images.unsplash.com/photo-1724257154172-b7dcef926dea?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHw0fHxjb3JhbCUyMHJlZWYlMjB1bmRlcndhdGVyJTIwcGFwdWF8ZW58MXx8fHwxNzc1ODM3MDY2fDA&ixlib=rb-4.1.0&q=80&w=1080',
                'author' => 'Tim Konservasi',
                'views' => '1,876',
                'content' => '<p>Tim survei melakukan monitoring kondisi ekosistem terumbu karang di kawasan konservasi Teluk Cenderawasih. Monitoring dilakukan untuk mengukur tutupan karang hidup dan kesehatan habitat laut.</p>
<p>Hasil awal menunjukkan tren positif pada beberapa lokasi inti konservasi. Pemerintah daerah akan melanjutkan program rehabilitasi dan edukasi masyarakat pesisir untuk menjaga keberlanjutan ekosistem.</p>',
            ],
        ];
    }
}