<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HeroBannerModel;
use App\Models\SitePageModel;
use CodeIgniter\HTTP\ResponseInterface;

class PengaturanBeranda extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index(): string
    {
        $sitePageModel = model(SitePageModel::class);
        $banners = [];

        if (HeroBannerModel::tableReady()) {
            $banners = model(HeroBannerModel::class)
                ->orderBy('sort_order', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll();
        }

        $modeSetting = $sitePageModel->findBySlug(SitePageModel::SLUG_PENGATURAN_HERO_SLIDE_MODE);
        $heroSlideMode = SitePageModel::HERO_SLIDE_MODE_BERITA;
        if ($modeSetting !== null && in_array($modeSetting['body'], [SitePageModel::HERO_SLIDE_MODE_BERITA, SitePageModel::HERO_SLIDE_MODE_BANNER], true)) {
            $heroSlideMode = $modeSetting['body'];
        }

        return view('admin/pengaturan_beranda/index', [
            'title'         => 'Pengaturan Beranda',
            'adminNav'      => 'pengaturan-beranda',
            'setting'       => $sitePageModel->findBySlug(SitePageModel::SLUG_PENGATURAN_BERANDA),
            'heroSlideMode' => $heroSlideMode,
            'banners'       => $banners,
        ]);
    }

    public function update(): ResponseInterface
    {
        $rules = [
            'hero_bg'         => 'permit_empty|uploaded[hero_bg]|is_image[hero_bg]|mime_in[hero_bg,image/jpg,image/jpeg,image/png,image/webp]|max_size[hero_bg,4096]',
            'hero_slide_mode' => 'required|in_list[berita,banner]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->handleUpload('hero_bg', SitePageModel::SLUG_PENGATURAN_BERANDA, 'Pengaturan Beranda', 'Pengaturan halaman utama');

        $slideMode = (string) $this->request->getPost('hero_slide_mode');
        $this->saveSitePageSetting(
            SitePageModel::SLUG_PENGATURAN_HERO_SLIDE_MODE,
            $slideMode,
            'Mode Slide Hero Beranda',
            'Menentukan konten slide tambahan di hero beranda'
        );

        return redirect()->to(base_url('admin/pengaturan-beranda'))
            ->with('success', 'Pengaturan beranda berhasil disimpan.');
    }

    public function createBanner(): string
    {
        return view('admin/pengaturan_beranda/banner_form', [
            'title'      => 'Tambah Banner Ucapan',
            'adminNav'   => 'pengaturan-beranda',
            'banner'     => null,
            'formAction' => base_url('admin/pengaturan-beranda/banner-ucapan/simpan'),
        ]);
    }

    public function storeBanner(): ResponseInterface
    {
        if (! $this->validate($this->bannerValidationRules(true))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $path = $this->storeBannerFile($this->request->getFile('banner_image'));
        if ($path === null) {
            return redirect()->back()->withInput()->with('errors', ['banner_image' => 'Gambar banner wajib diunggah (maks. 5MB, JPG/PNG/WebP).']);
        }

        model(HeroBannerModel::class)->insert($this->bannerPayloadFromRequest($path));

        return redirect()->to(base_url('admin/pengaturan-beranda'))->with('success', 'Banner ucapan berhasil ditambahkan.');
    }

    public function editBanner(int $id): ResponseInterface|string
    {
        $row = model(HeroBannerModel::class)->find($id);
        if ($row === null) {
            return redirect()->to(base_url('admin/pengaturan-beranda'))->with('error', 'Banner ucapan tidak ditemukan.');
        }

        return view('admin/pengaturan_beranda/banner_form', [
            'title'      => 'Edit Banner Ucapan',
            'adminNav'   => 'pengaturan-beranda',
            'banner'     => $row,
            'formAction' => base_url('admin/pengaturan-beranda/banner-ucapan/' . $id . '/update'),
        ]);
    }

    public function updateBanner(int $id): ResponseInterface
    {
        $model = model(HeroBannerModel::class);
        $existing = $model->find($id);
        if ($existing === null) {
            return redirect()->to(base_url('admin/pengaturan-beranda'))->with('error', 'Banner ucapan tidak ditemukan.');
        }

        if (! $this->validate($this->bannerValidationRules(false))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $previousImage = (string) ($existing['image'] ?? '');
        $newPath = $this->resolveBannerUpload($previousImage);
        if ($newPath === null) {
            return redirect()->back()->withInput()->with('errors', ['banner_image' => 'Gambar banner tidak valid. Unggah file baru atau pertahankan gambar saat ini.']);
        }

        $model->update($id, $this->bannerPayloadFromRequest($newPath));

        if ($newPath !== $previousImage && $this->isStoredBannerPath($previousImage)) {
            $this->deleteBannerFile($previousImage);
        }

        return redirect()->to(base_url('admin/pengaturan-beranda'))->with('success', 'Banner ucapan berhasil diperbarui.');
    }

    public function deleteBanner(int $id): ResponseInterface
    {
        $model = model(HeroBannerModel::class);
        $row = $model->find($id);
        if ($row === null) {
            return redirect()->to(base_url('admin/pengaturan-beranda'))->with('error', 'Banner ucapan tidak ditemukan.');
        }

        $img = (string) ($row['image'] ?? '');
        $model->delete($id);

        if ($this->isStoredBannerPath($img)) {
            $this->deleteBannerFile($img);
        }

        return redirect()->to(base_url('admin/pengaturan-beranda'))->with('success', 'Banner ucapan berhasil dihapus.');
    }

    /**
     * @return array<string, string>
     */
    private function bannerValidationRules(bool $isCreate): array
    {
        $rules = [
            'title'      => 'required|max_length[255]',
            'sort_order' => 'permit_empty|integer',
            'is_active'  => 'permit_empty|in_list[0,1]',
        ];

        if ($isCreate) {
            $rules['banner_image'] = 'uploaded[banner_image]|is_image[banner_image]|mime_in[banner_image,image/jpg,image/jpeg,image/png,image/webp]|max_size[banner_image,5120]';
        } else {
            $rules['banner_image'] = 'permit_empty|uploaded[banner_image]|is_image[banner_image]|mime_in[banner_image,image/jpg,image/jpeg,image/png,image/webp]|max_size[banner_image,5120]';
        }

        return $rules;
    }

    /**
     * @return array<string, int|string|null>
     */
    private function bannerPayloadFromRequest(string $imagePath): array
    {
        return [
            'title'      => (string) $this->request->getPost('title'),
            'image'      => $imagePath,
            'sort_order' => max(0, (int) $this->request->getPost('sort_order')),
            'is_active'  => (int) $this->request->getPost('is_active'),
        ];
    }

    private function resolveBannerUpload(string $previousStored): ?string
    {
        $file = $this->request->getFile('banner_image');
        if ($file !== null && $file->isValid() && ! $file->hasMoved()) {
            $stored = $this->storeBannerFile($file);

            return $stored ?? ($previousStored !== '' ? $previousStored : null);
        }

        $current = trim((string) $this->request->getPost('current_image'));

        return $current !== '' ? $current : ($previousStored !== '' ? $previousStored : null);
    }

    private function storeBannerFile(?object $file): ?string
    {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ext = strtolower($file->getClientExtension() ?: '');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return null;
        }

        $sizeBytes = (int) $file->getSize();
        if ($sizeBytes <= 0 || $sizeBytes > 5 * 1024 * 1024) {
            return null;
        }

        $targetDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'hero' . DIRECTORY_SEPARATOR . 'banners';
        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            return null;
        }

        $newName = $file->getRandomName();
        $file->move($targetDir, $newName);

        return 'uploads/hero/banners/' . $newName;
    }

    private function isStoredBannerPath(string $stored): bool
    {
        $stored = trim($stored);
        if ($stored === '' || preg_match('#^https?://#i', $stored) === 1) {
            return false;
        }

        return str_starts_with(ltrim($stored, '/'), 'uploads/hero/banners/');
    }

    private function deleteBannerFile(string $stored): void
    {
        if (! $this->isStoredBannerPath($stored)) {
            return;
        }

        $path = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($stored, '/'));
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function handleUpload(string $inputName, string $slug, string $title, string $description): void
    {
        $model = model(SitePageModel::class);
        $setting = $model->findBySlug($slug);

        $imageFile = $this->request->getFile($inputName);
        $bodyVal = $setting['body'] ?? '';

        if ($imageFile !== null && $imageFile->isValid() && ! $imageFile->hasMoved()) {
            $newName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'uploads/hero', $newName);

            if (! empty($setting['body']) && file_exists(FCPATH . $setting['body'])) {
                @unlink(FCPATH . $setting['body']);
            }

            $bodyVal = 'uploads/hero/' . $newName;
        }

        $this->saveSitePageSetting($slug, $bodyVal, $title, $description, $setting);
    }

    /**
     * @param array<string, mixed>|null $existing
     */
    private function saveSitePageSetting(string $slug, string $body, string $title, string $description, ?array $existing = null): void
    {
        $model = model(SitePageModel::class);
        if ($existing === null) {
            $existing = $model->findBySlug($slug);
        }

        if ($existing === null) {
            $model->insert([
                'slug'        => $slug,
                'title'       => $title,
                'description' => $description,
                'body'        => $body,
            ]);
        } else {
            $model->update($existing['id'], [
                'body' => $body,
            ]);
        }
    }
}
