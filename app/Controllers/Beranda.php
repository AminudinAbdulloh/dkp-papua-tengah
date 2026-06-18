<?php

namespace App\Controllers;

use App\Models\BerandaModel;
use App\Models\InformationObjectionModel;
use App\Models\InformationRequestModel;
use App\Models\NewsArticleModel;
use App\Models\PublicInformationModel;
use App\Models\PengumumanModel;
use App\Models\PublicationCategoryModel;
use App\Models\PublicationTypeModel;
use CodeIgniter\HTTP\ResponseInterface;

class Beranda extends BaseController
{
    protected BerandaModel $berandaModel;

    public function __construct(
        ?BerandaModel $berandaModel = null
    ) {
        $this->berandaModel = $berandaModel ?? new BerandaModel();
    }



    public function portal(): string
    {
        $heroBg  = null;
        $setting = model(\App\Models\SitePageModel::class)->findBySlug(\App\Models\SitePageModel::SLUG_PENGATURAN_BERANDA);
        if ($setting !== null && ! empty($setting['body'])) {
            $heroBg = base_url($setting['body']);
        }

        return view('public/portal', [
            'heroBg'       => $heroBg,
            'footerData'   => $this->berandaModel->getPublicFooterData(),
            'menuNavigasi' => $this->berandaModel->getPublicNavigationMenu(),
        ]);
    }

    public function index(): string
    {
        $heroBg = null;
        $setting = model(\App\Models\SitePageModel::class)->findBySlug(\App\Models\SitePageModel::SLUG_PENGATURAN_BERANDA);
        if ($setting !== null && !empty($setting['body'])) {
            $heroBg = base_url($setting['body']);
        }

        $newsList = $this->berandaModel->getNewsList(3);

        $data = [
            'heroBg'        => $heroBg,
            'heroSlides'    => $newsList,
            'services'      => $this->berandaModel->getServices(),
            'newsList'      => $newsList,
            'galleryPhotos' => $this->berandaModel->getGalleryPhotos(8),
            'latestVideos'  => $this->berandaModel->getLatestVideos(),
            'menuNavigasi'  => $this->berandaModel->getPublicNavigationMenu(),
            'footerData'    => $this->berandaModel->getPublicFooterData(),
        ];

        return view('public/index', $data);
    }


    public function page(string $slug, ?string $subSlug = null): string
    {
        $path = $subSlug ? $slug . '/' . $subSlug : $slug;
        $pageData = $this->berandaModel->getPublicPageData($path);

        if ($pageData === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'menuNavigasi' => $this->berandaModel->getPublicNavigationMenu(),
            'footerData' => $this->berandaModel->getPublicFooterData(),
            'pageData' => $pageData,
        ];

        return view('public/page', $data);
    }

    public function berita(): string
    {
        $newsList = $this->berandaModel->getNewsList(9);
        $pager = null;
        if (NewsArticleModel::tableReady()) {
            $pager = model(NewsArticleModel::class)->pager;
        }

        $data = [
            'menuNavigasi' => $this->berandaModel->getPublicNavigationMenu(),
            'footerData' => $this->berandaModel->getPublicFooterData(),
            'newsList' => $newsList,
            'pager' => $pager,
            'pageData' => [
                'title' => 'Berita',
                'description' => 'Informasi dan kegiatan terbaru Dinas Kelautan dan Perikanan Provinsi Papua Tengah.',
                'breadcrumbs' => [
                    ['label' => 'Beranda', 'href' => base_url('/beranda')],
                    ['label' => 'Berita', 'href' => null],
                ],
            ],
        ];

        return view('public/berita', $data);
    }

    public function beritaDetail(int $id): string
    {
        $news = $this->berandaModel->getNewsDetail($id);

        if ($news === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (NewsArticleModel::tableReady()) {
            model(NewsArticleModel::class)->recordReaderVisitIfNewSession($id);
            $news = $this->berandaModel->getNewsDetail($id) ?? $news;
        }

        $data = [
            'menuNavigasi' => $this->berandaModel->getPublicNavigationMenu(),
            'footerData' => $this->berandaModel->getPublicFooterData(),
            'news' => $news,
            'popularNews' => $this->berandaModel->getPopularNews((int) $news['id']),
            'pageData' => [
                'title' => $news['title'],
                'description' => 'Berita terbaru Dinas Kelautan dan Perikanan Papua Tengah.',
                'backgroundImage' => $news['image'] ?? null,
                'breadcrumbs' => [
                    ['label' => 'Beranda', 'href' => base_url('/beranda')],
                    ['label' => 'Berita', 'href' => base_url('berita')],
                    ['label' => $news['title'], 'href' => null],
                ],
            ],
        ];

        return view('public/berita_detail', $data);
    }

    public function galeriFoto(): string
    {
        $galleryPhotos = $this->berandaModel->getGalleryPhotos(8);
        $pager = null;
        if (model(\App\Models\GalleryPhotoModel::class)->tableReady()) {
            $pager = model(\App\Models\GalleryPhotoModel::class)->pager;
        }

        $data = [
            'menuNavigasi' => $this->berandaModel->getPublicNavigationMenu(),
            'footerData' => $this->berandaModel->getPublicFooterData(),
            'galleryPhotos' => $galleryPhotos,
            'pager' => $pager,
            'pageData' => [
                'title' => 'Galeri Foto',
                'description' => 'Dokumentasi visual kegiatan dan potensi sektor kelautan dan perikanan Papua Tengah.',
                'breadcrumbs' => [
                    ['label' => 'Beranda', 'href' => base_url('/beranda')],
                    ['label' => 'Galeri Foto', 'href' => null],
                ],
            ],
        ];

        return view('public/galeri_foto', $data);
    }

    public function galeriFotoDetail(int $id): string
    {
        $photo = $this->berandaModel->getGalleryPhotoDetail($id);

        if ($photo === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'menuNavigasi' => $this->berandaModel->getPublicNavigationMenu(),
            'footerData' => $this->berandaModel->getPublicFooterData(),
            'photo' => $photo,
            'relatedPhotos' => $this->berandaModel->getRelatedGalleryPhotos((int) $photo['id']),
            'pageData' => [
                'title' => $photo['title'],
                'description' => 'Detail dokumentasi kegiatan dan potensi sektor kelautan dan perikanan Papua Tengah.',
                'backgroundImage' => $photo['image'] ?? null,
                'breadcrumbs' => [
                    ['label' => 'Beranda', 'href' => base_url('/beranda')],
                    ['label' => 'Galeri Foto', 'href' => base_url('galeri/foto')],
                    ['label' => $photo['title'], 'href' => null],
                ],
            ],
        ];

        return view('public/galeri_foto_detail', $data);
    }

    public function galeriVideo(): string
    {
        $latestVideos = $this->berandaModel->getLatestVideos(6);
        $pager = null;
        if (model(\App\Models\GalleryVideoModel::class)->tableReady()) {
            $pager = model(\App\Models\GalleryVideoModel::class)->pager;
        }

        $data = [
            'menuNavigasi' => $this->berandaModel->getPublicNavigationMenu(),
            'footerData' => $this->berandaModel->getPublicFooterData(),
            'latestVideos' => $latestVideos,
            'pager' => $pager,
            'pageData' => [
                'title' => 'Galeri Video',
                'description' => 'Kumpulan video kegiatan, edukasi, dan profil sektor kelautan serta perikanan Papua Tengah.',
                'breadcrumbs' => [
                    ['label' => 'Beranda', 'href' => base_url('/beranda')],
                    ['label' => 'Galeri Video', 'href' => null],
                ],
            ],
        ];

        return view('public/galeri_video', $data);
    }

    public function pengumuman(): string
    {
        $pengumuman = [];
        $pager = null;
        try {
            $model = model(PengumumanModel::class);
            $pengumuman = $model->orderBy('id', 'DESC')->paginate(10, 'public');
            $pager = $model->pager;
        } catch (\Throwable) {
        }

        $data = [
            'menuNavigasi' => $this->berandaModel->getPublicNavigationMenu(),
            'footerData'   => $this->berandaModel->getPublicFooterData(),
            'pengumuman'   => $pengumuman,
            'pager'        => $pager,
            'pageData'     => [
                'title'       => 'Pengumuman',
                'description' => 'Pengumuman resmi dan edaran terkait layanan Dinas Kelautan dan Perikanan Provinsi Papua Tengah.',
                'breadcrumbs' => [
                    ['label' => 'Beranda', 'href' => base_url('/beranda')],
                    ['label' => 'Pengumuman', 'href' => null],
                ],
            ],
        ];

        return view('public/pengumuman', $data);
    }

    public function pengumumanDetail(int $id): string
    {
        $pengumuman = null;
        try {
            $pengumuman = model(PengumumanModel::class)->find($id);
        } catch (\Throwable) {
        }

        if ($pengumuman === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $title = (string) ($pengumuman['judul'] ?? 'Detail Pengumuman');

        $data = [
            'menuNavigasi' => $this->berandaModel->getPublicNavigationMenu(),
            'footerData'   => $this->berandaModel->getPublicFooterData(),
            'pengumuman'   => $pengumuman,
            'pageData'     => [
                'title'       => $title,
                'description' => 'Detail pengumuman resmi Dinas Kelautan dan Perikanan Provinsi Papua Tengah.',
                'breadcrumbs' => [
                    ['label' => 'Beranda', 'href' => base_url('/beranda')],
                    ['label' => 'Pengumuman', 'href' => base_url('pengumuman')],
                    ['label' => $title, 'href' => null],
                ],
            ],
        ];

        return view('public/pengumuman_detail', $data);
    }

    /**
     * Informasi Publik – list by category.
     */
    public function informasiPublik(?string $categorySlug = null): string
    {
        $categoryMap = [
            'informasi-berkala'      => PublicInformationModel::CATEGORY_BERKALA,
            'informasi-serta-merta'  => PublicInformationModel::CATEGORY_SERTA_MERTA,
            'informasi-setiap-saat'  => PublicInformationModel::CATEGORY_SETIAP_SAAT,
            'informasi-dikecualikan' => PublicInformationModel::CATEGORY_DIKECUALIKAN,
        ];

        $modelCategory = $categoryMap[$categorySlug] ?? null;
        $pageTitle = match ($modelCategory) {
            PublicInformationModel::CATEGORY_BERKALA      => 'Informasi Berkala',
            PublicInformationModel::CATEGORY_SERTA_MERTA  => 'Informasi Serta Merta',
            PublicInformationModel::CATEGORY_SETIAP_SAAT  => 'Informasi Setiap Saat',
            PublicInformationModel::CATEGORY_DIKECUALIKAN => 'Informasi yang Dikecualikan',
            default => 'Daftar Informasi Publik',
        };

        $infoItems = [];
        if (PublicInformationModel::tableReady()) {
            $infoItems = model(PublicInformationModel::class)->getPublishedForPublic($modelCategory);
        }

        if ($modelCategory === PublicInformationModel::CATEGORY_BERKALA) {
            $profilItems = [
                [
                    'custom_url' => base_url('profil/sejarah'),
                    'title' => 'Sejarah Dinas',
                    'description' => 'Informasi mengenai sejarah berdirinya Dinas Kelautan dan Perikanan Papua Tengah',
                    'responsible_party' => 'Dinas Kelautan dan Perikanan',
                    'time_period' => 'Setiap Saat',
                    'information_format' => 'Teks / Halaman Web',
                    'year' => date('Y'),
                ],
                [
                    'custom_url' => base_url('profil/visi-misi'),
                    'title' => 'Visi dan Misi',
                    'description' => 'Visi dan Misi Dinas Kelautan dan Perikanan Papua Tengah',
                    'responsible_party' => 'Dinas Kelautan dan Perikanan',
                    'time_period' => 'Setiap Saat',
                    'information_format' => 'Teks / Halaman Web',
                    'year' => date('Y'),
                ],
                [
                    'custom_url' => base_url('profil/tupoksi'),
                    'title' => 'Tugas Pokok dan Fungsi',
                    'description' => 'Tugas pokok dan fungsi Dinas Kelautan dan Perikanan Papua Tengah',
                    'responsible_party' => 'Dinas Kelautan dan Perikanan',
                    'time_period' => 'Setiap Saat',
                    'information_format' => 'Teks / Halaman Web',
                    'year' => date('Y'),
                ],
                [
                    'custom_url' => base_url('profil/struktur'),
                    'title' => 'Struktur Organisasi',
                    'description' => 'Struktur organisasi Dinas Kelautan dan Perikanan Papua Tengah',
                    'responsible_party' => 'Dinas Kelautan dan Perikanan',
                    'time_period' => 'Setiap Saat',
                    'information_format' => 'Bagan / Teks',
                    'year' => date('Y'),
                ],
                [
                    'custom_url' => base_url('profil/pejabat'),
                    'title' => 'Profil Pejabat',
                    'description' => 'Profil pejabat struktural di lingkungan Dinas',
                    'responsible_party' => 'Bagian Kepegawaian',
                    'time_period' => 'Setiap Saat',
                    'information_format' => 'Teks / Foto',
                    'year' => date('Y'),
                ],
                [
                    'custom_url' => base_url('profil/kontak'),
                    'title' => 'Alamat dan Kontak',
                    'description' => 'Informasi alamat kantor, email, dan telepon Dinas',
                    'responsible_party' => 'Dinas Kelautan dan Perikanan',
                    'time_period' => 'Setiap Saat',
                    'information_format' => 'Teks',
                    'year' => date('Y'),
                ],
            ];
            $infoItems = array_merge($profilItems, $infoItems);
        }

        // Search filter
        $searchQuery = trim((string) $this->request->getGet('cari'));
        if ($searchQuery !== '' && $infoItems !== []) {
            $infoItems = array_values(array_filter($infoItems, static function (array $item) use ($searchQuery): bool {
                $haystack = strtolower(
                    ((string) ($item['title'] ?? ''))
                    . ' ' . ((string) ($item['description'] ?? ''))
                    . ' ' . ((string) ($item['responsible_party'] ?? ''))
                );
                return str_contains($haystack, strtolower($searchQuery));
            }));
        }

        $breadcrumbs = [
            ['label' => 'Beranda', 'href' => base_url('/beranda')],
            ['label' => 'PPID', 'href' => null],
        ];
        if ($modelCategory !== null) {
            $breadcrumbs[] = ['label' => $pageTitle, 'href' => null];
        }

        $page = (int) ($this->request->getGet('page_public') ?? 1);
        if ($page < 1) $page = 1;
        $perPage = 10;
        $total = count($infoItems);
        $pager = \Config\Services::pager();
        $pagerLinks = $pager->makeLinks($page, $perPage, $total, 'bootstrap_pagination', 0, 'public');

        $infoItemsPaginated = array_slice($infoItems, ($page - 1) * $perPage, $perPage);

        $data = [
            'menuNavigasi'      => $this->berandaModel->getPublicNavigationMenu(),
            'footerData'        => $this->berandaModel->getPublicFooterData(),
            'infoItems'         => $infoItemsPaginated,
            'pagerLinks'        => $pagerLinks,
            'startNo'           => ($page - 1) * $perPage + 1,
            'currentCategory'   => $modelCategory,
            'currentCategorySlug' => $categorySlug,
            'searchQuery'       => $searchQuery,
            'pageData'          => [
                'title'       => $pageTitle,
                'description' => 'Informasi publik yang disediakan oleh Dinas Kelautan dan Perikanan Provinsi Papua Tengah.',
                'breadcrumbs' => $breadcrumbs,
            ],
        ];

        return view('public/informasi_publik', $data);
    }

    /**
     * Publikasi – list documents by type.
     */
    public function publikasiList(string $typeSlug): string
    {
        $pubTypeModel = model(\App\Models\PublicationTypeModel::class);
        $pubType      = null;
        try {
            $pubType = $pubTypeModel->findBySlug($typeSlug);
        } catch (\Throwable) {
            // tabel belum siap, lanjut
        }

        if ($pubType === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $typeName = (string) $pubType['name'];

        $documents = [];
        if (PublicInformationModel::tableReady()) {
            $documents = model(PublicInformationModel::class)->getPublishedByPubType($typeSlug);
        }

        // Sub-category filter
        $subCatSlug = trim((string) $this->request->getGet('sub'));
        if ($subCatSlug !== '' && $documents !== []) {
            $documents = array_values(array_filter($documents, static function (array $doc) use ($subCatSlug): bool {
                return ((string) ($doc['pub_cat_slug'] ?? '')) === $subCatSlug;
            }));
        }

        // Search filter
        $searchQuery = trim((string) $this->request->getGet('cari'));
        if ($searchQuery !== '' && $documents !== []) {
            $documents = array_values(array_filter($documents, static function (array $doc) use ($searchQuery): bool {
                $haystack = strtolower(((string) ($doc['title'] ?? '')) . ' ' . ((string) ($doc['description'] ?? '')));
                return str_contains($haystack, strtolower($searchQuery));
            }));
        }

        $page = (int) ($this->request->getGet('page_public') ?? 1);
        if ($page < 1) $page = 1;
        $perPage = 10;
        $total = count($documents);
        $pager = \Config\Services::pager();
        $pagerLinks = $pager->makeLinks($page, $perPage, $total, 'bootstrap_pagination', 0, 'public');

        $documentsPaginated = array_slice($documents, ($page - 1) * $perPage, $perPage);

        // Sub-kategori publikasi untuk sidebar (difilter berdasarkan tipe aktif)
        $allPubCategories = [];
        try {
            if (PublicationCategoryModel::tableReady()) {
                $allPubCategories = model(PublicationCategoryModel::class)
                    ->where('publication_type', $typeSlug)
                    ->orderBy('sort_order', 'ASC')
                    ->orderBy('name', 'ASC')
                    ->findAll();
            }
        } catch (\Throwable) {
        }

        $data = [
            'menuNavigasi'      => $this->berandaModel->getPublicNavigationMenu(),
            'footerData'        => $this->berandaModel->getPublicFooterData(),
            'documents'         => $documentsPaginated,
            'pagerLinks'        => $pagerLinks,
            'currentTypeSlug'   => $typeSlug,
            'currentTypeName'   => $typeName,
            'currentSubSlug'    => $subCatSlug,
            'allPubCategories'  => $allPubCategories,
            'searchQuery'       => $searchQuery,
            'breadcrumbs'       => [
                ['label' => 'Beranda',   'href' => base_url('/beranda')],
                ['label' => 'Publikasi', 'href' => null],
                ['label' => $typeName,   'href' => null],
            ],
            'pageData'          => [
                'title'       => 'Publikasi',
                'description' => 'Daftar dokumen publikasi ' . $typeName . '.',
                'breadcrumbs' => [
                    ['label' => 'Beranda',  'href' => base_url('/beranda')],
                    ['label' => $typeName,  'href' => null],
                ],
            ],
        ];

        return view('public/publikasi_list', $data);
    }

    /**
     * Publikasi – detail view of a single document.
     */
    public function publikasiDetail(string $typeSlug, int $id): string
    {
        // Coba cari tipe di tabel publication_types (boleh kosong/tidak ada)
        $pubTypeModel = model(\App\Models\PublicationTypeModel::class);
        $pubType      = null;
        try {
            $pubType = $pubTypeModel->findBySlug($typeSlug);
        } catch (\Throwable) {
            // tabel belum siap, lanjut
        }

        // Ambil dokumen terlebih dahulu
        $document = null;
        if (PublicInformationModel::tableReady()) {
            $document = model(PublicInformationModel::class)->getPublishedById($id);
        }

        if ($document === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Tentukan nama tipe: dari tabel → dari dokumen → dari slug
        $typeName = (string) ($pubType['name']
            ?? $document['pub_cat_name']
            ?? ucwords(str_replace('-', ' ', $typeSlug)));

        $docTitle = (string) ($document['title'] ?? $typeName);

        // Sub-kategori publikasi untuk sidebar (difilter berdasarkan tipe aktif)
        $allPubCategories = [];
        try {
            if (PublicationCategoryModel::tableReady()) {
                $allPubCategories = model(PublicationCategoryModel::class)
                    ->where('publication_type', $typeSlug)
                    ->orderBy('sort_order', 'ASC')
                    ->orderBy('name', 'ASC')
                    ->findAll();
            }
        } catch (\Throwable) {
        }

        // Tentukan sub-kategori aktif dari dokumen
        $currentPubCategoryId = (int) ($document['publication_category_id'] ?? 0);

        $data = [
            'menuNavigasi'         => $this->berandaModel->getPublicNavigationMenu(),
            'footerData'           => $this->berandaModel->getPublicFooterData(),
            'document'             => $document,
            'currentTypeSlug'      => $typeSlug,
            'currentTypeName'      => $typeName,
            'allPubCategories'     => $allPubCategories,
            'currentPubCategoryId' => $currentPubCategoryId,
            'breadcrumbs'          => [
                ['label' => 'Beranda',    'href' => base_url('/beranda')],
                ['label' => 'Publikasi',  'href' => null],
                ['label' => $typeName,    'href' => base_url('publikasi/' . $typeSlug)],
                ['label' => $docTitle,    'href' => null],
            ],
            'pageData'             => [
                'title'       => $docTitle,
                'description' => 'Detail dokumen publikasi.',
                'breadcrumbs' => [
                    ['label' => 'Beranda',  'href' => base_url('/beranda')],
                    ['label' => $docTitle,  'href' => null],
                ],
            ],
        ];

        return view('public/publikasi_detail', $data);
    }

    /**
     * Handle form permohonan informasi submission.
     */
    public function submitPermohonan(): ResponseInterface
    {
        $rules = [
            'kategori'          => 'required|in_list[Perorangan,Lembaga]',
            'nama'              => 'required|max_length[255]',
            'pekerjaan'         => 'required|max_length[255]',
            'alamat'            => 'required|max_length[2000]',
            'identitas'         => 'required|max_length[50]',
            'nomor_identitas'   => 'required|max_length[100]',
            'telepon'           => 'required|max_length[30]',
            'email'             => 'required|valid_email|max_length[255]',
            'rincian_informasi' => 'required|max_length[5000]',
            'tujuan_informasi'  => 'required|max_length[5000]',
            'cara_mendapatkan'  => 'required|in_list[membaca,salinan]',
            'cara_salinan'      => 'required|in_list[langsung,kurir,pos,faksimili,email]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = model(InformationRequestModel::class);
        $regNumber = $model->generateRegistrationNumber();

        $model->insert([
            'registration_number' => $regNumber,
            'applicant_category'  => (string) $this->request->getPost('kategori'),
            'name'                => (string) $this->request->getPost('nama'),
            'occupation'          => (string) $this->request->getPost('pekerjaan'),
            'address'             => (string) $this->request->getPost('alamat'),
            'identity_type'       => (string) $this->request->getPost('identitas'),
            'identity_number'     => (string) $this->request->getPost('nomor_identitas'),
            'phone'               => (string) $this->request->getPost('telepon'),
            'email'               => (string) $this->request->getPost('email'),
            'information_detail'  => (string) $this->request->getPost('rincian_informasi'),
            'information_purpose' => (string) $this->request->getPost('tujuan_informasi'),
            'obtain_method'       => (string) $this->request->getPost('cara_mendapatkan'),
            'copy_method'         => (string) $this->request->getPost('cara_salinan'),
            'status'              => InformationRequestModel::STATUS_DITERIMA,
        ]);

        return redirect()->to(base_url('layanan/form-permohonan-informasi'))
            ->with('permohonan_success', 'Permohonan informasi berhasil dikirim. Nomor registrasi Anda: ' . $regNumber . '. Simpan nomor ini untuk melacak status permohonan.');
    }

    /**
     * Handle tracking permohonan informasi.
     */
    public function lacakPermohonan(): string
    {
        $query = trim((string) $this->request->getPost('query_lacak'));

        $trackResults = [];
        if ($query !== '' && InformationRequestModel::tableReady()) {
            $trackResults = model(InformationRequestModel::class)->trackByQuery($query);
        }

        $pageData = $this->berandaModel->getPublicPageData('layanan/form-permohonan-informasi');
        if ($pageData === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'menuNavigasi' => $this->berandaModel->getPublicNavigationMenu(),
            'footerData'   => $this->berandaModel->getPublicFooterData(),
            'pageData'     => $pageData,
            'trackQuery'   => $query,
            'trackResults' => $trackResults,
            'activeLacakTab' => true,
        ];

        return view('public/page', $data);
    }

    /**
     * Handle form keberatan informasi submission.
     */
    public function submitKeberatan(): ResponseInterface
    {
        $rules = [
            'nama'            => 'required|max_length[255]',
            'identitas'       => 'required|max_length[50]',
            'nomor_identitas' => 'required|max_length[100]',
            'alamat'          => 'required|max_length[2000]',
            'telepon'         => 'required|max_length[30]',
            'alasan'          => 'required|in_list[' . implode(',', InformationObjectionModel::validReasons()) . ']',
            'kasus_posisi'    => 'required|max_length[10000]',
            'no_registrasi_permohonan' => 'permit_empty|max_length[50]',
            'lampiran'        => 'permit_empty|uploaded[lampiran]|max_size[lampiran,10240]|ext_in[lampiran,pdf,doc,docx,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = model(InformationObjectionModel::class);
        $regNumber = $model->generateRegistrationNumber();

        // Handle file upload
        $attachPath = null;
        $attachName = null;
        $file = $this->request->getFile('lampiran');
        if ($file !== null && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/keberatan', $newName);
            $attachPath = 'uploads/keberatan/' . $newName;
            $attachName = $file->getClientName();
        }

        $model->insert([
            'registration_number'          => $regNumber,
            'name'                         => (string) $this->request->getPost('nama'),
            'identity_type'                => (string) $this->request->getPost('identitas'),
            'identity_number'              => (string) $this->request->getPost('nomor_identitas'),
            'address'                      => (string) $this->request->getPost('alamat'),
            'phone'                        => (string) $this->request->getPost('telepon'),
            'objection_reason'             => (string) $this->request->getPost('alasan'),
            'case_description'             => (string) $this->request->getPost('kasus_posisi'),
            'request_registration_number'  => trim((string) $this->request->getPost('no_registrasi_permohonan')) ?: null,
            'attachment_path'              => $attachPath,
            'attachment_name'              => $attachName,
            'status'                       => InformationObjectionModel::STATUS_DITERIMA,
        ]);

        return redirect()->to(base_url('layanan/form-keberatan-informasi'))
            ->with('keberatan_success', 'Keberatan berhasil dikirim. Nomor registrasi: ' . $regNumber . '. Keberatan akan diproses maksimal 30 hari kerja.');
    }

    public function download(): string
    {
        $downloadModel = model(\App\Models\DownloadModel::class);
        $search = $this->request->getGet('cari');
        
        $query = $downloadModel;
        if ($search) {
            $query = $query->like('title', $search);
        }

        $data = [
            'menuNavigasi' => $this->berandaModel->getPublicNavigationMenu(),
            'footerData'   => $this->berandaModel->getPublicFooterData(),
            'downloads'    => $query->orderBy('created_at', 'DESC')->paginate(10, 'public'),
            'pager'        => $downloadModel->pager,
            'searchQuery'  => $search,
            'pageData'     => [
                'title'       => 'Pusat Unduhan',
                'description' => 'Kumpulan dokumen, regulasi, dan informasi resmi yang dapat diunduh publik.',
                'breadcrumbs' => [
                    ['label' => 'Beranda', 'href' => base_url('/beranda')],
                    ['label' => 'Download', 'href' => null],
                ],
            ],
        ];

        return view('public/download', $data);
    }

    public function doDownload(int $id): ResponseInterface
    {
        $downloadModel = model(\App\Models\DownloadModel::class);
        $item = $downloadModel->find($id);

        if (!$item) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Increment count
        $downloadModel->update($id, ['download_count' => $item['download_count'] + 1]);

        return $this->response->download(FCPATH . 'uploads/downloads/' . $item['file_path'], null)
            ->setFileName($item['title'] . '.' . $item['file_type']);
    }
}