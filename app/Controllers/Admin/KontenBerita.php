<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NewsArticleModel;
use CodeIgniter\HTTP\ResponseInterface;

class KontenBerita extends BaseController
{
    protected $helpers = ['form', 'url', 'content'];

    public function index(): string
    {
        $model = model(NewsArticleModel::class);
        $q = (string) $this->request->getGet('q');

        if ($q !== '') {
            $model->groupStart()
                ->like('title', $q)
                ->orLike('excerpt', $q)
                ->orLike('author', $q)
                ->groupEnd();
        }

        $rows = $model->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate(10, 'admin');

        $settingModel = model(\App\Models\SitePageModel::class);
        $setting = $settingModel->findBySlug('pengaturan/exclusive-news-limit');
        $exclusiveLimit = $setting !== null ? (int)$setting['body'] : 5;

        return view('admin/konten/berita_index', [
            'title'          => 'Kelola Berita',
            'adminNav'       => 'konten-berita',
            'articles'       => $rows,
            'pager'          => $model->pager,
            'searchQuery'    => $q,
            'exclusiveLimit' => $exclusiveLimit,
        ]);
    }

    public function create(): string
    {
        return view('admin/konten/berita_form', [
            'title'    => 'Tambah Berita',
            'adminNav' => 'konten-berita',
            'article'  => null,
            'formAction' => base_url('admin/konten/berita/simpan'),
        ]);
    }

    public function store(): ResponseInterface
    {
        if (! $this->validate($this->textValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $path = $this->resolveFeaturedUpload(true, null);
        if ($path === null) {
            return redirect()->back()->withInput()->with('errors', ['featured_image' => 'Gambar utama wajib diunggah (maks. 5MB, JPG/PNG/WebP/GIF).']);
        }

        $model = model(NewsArticleModel::class);
        $model->insert(array_merge($this->articlePayloadFromRequest(), [
            'image' => $path,
            'views' => 0,
        ]));

        cleanup_unused_editor_uploads();

        return redirect()->to(base_url('admin/konten/berita'))->with('message', 'Berita berhasil ditambahkan.');
    }

    public function updateExclusiveLimit(): ResponseInterface
    {
        $limit = (int) $this->request->getPost('exclusive_limit');
        if ($limit < 1) {
            $limit = 5;
        }

        $settingModel = model(\App\Models\SitePageModel::class);
        $setting = $settingModel->findBySlug('pengaturan/exclusive-news-limit');

        if ($setting === null) {
            $settingModel->insert([
                'slug'        => 'pengaturan/exclusive-news-limit',
                'title'       => 'Limit Berita Eksklusif',
                'description' => 'Jumlah maksimal berita eksklusif yang ditampilkan di halaman berita',
                'body'        => (string) $limit,
            ]);
        } else {
            $settingModel->update($setting['id'], [
                'body' => (string) $limit,
            ]);
        }

        return redirect()->to(base_url('admin/konten/berita'))->with('message', 'Limit berita eksklusif berhasil diperbarui.');
    }

    public function edit(int $id): ResponseInterface|string
    {
        $row = model(NewsArticleModel::class)->find($id);
        if ($row === null) {
            return redirect()->to(base_url('admin/konten/berita'))->with('error', 'Berita tidak ditemukan.');
        }

        return view('admin/konten/berita_form', [
            'title'    => 'Edit Berita',
            'adminNav' => 'konten-berita',
            'article'  => $row,
            'formAction' => base_url('admin/konten/berita/' . $id . '/update'),
        ]);
    }

    public function update(int $id): ResponseInterface
    {
        $model = model(NewsArticleModel::class);
        $existing = $model->find($id);
        if ($existing === null) {
            return redirect()->to(base_url('admin/konten/berita'))->with('error', 'Berita tidak ditemukan.');
        }

        if (! $this->validate($this->textValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $previousImage = (string) ($existing['image'] ?? '');
        $newPath = $this->resolveFeaturedUpload(false, $previousImage);
        if ($newPath === null) {
            return redirect()->back()->withInput()->with('errors', ['featured_image' => 'Gambar utama tidak valid. Unggah file gambar atau pertahankan gambar saat ini.']);
        }

        $payload = $this->articlePayloadFromRequest();
        $payload['image'] = $newPath;

        $model->update($id, $payload);

        if ($newPath !== $previousImage && $this->isStoredNewsFeaturedPath($previousImage)) {
            $this->deleteNewsFeaturedFile($previousImage);
        }

        cleanup_unused_editor_uploads();

        return redirect()->to(base_url('admin/konten/berita'))->with('message', 'Berita berhasil diperbarui.');
    }

    public function delete(int $id): ResponseInterface
    {
        $model = model(NewsArticleModel::class);
        $row = $model->find($id);
        if ($row === null) {
            return redirect()->to(base_url('admin/konten/berita'))->with('error', 'Berita tidak ditemukan.');
        }

        $img = (string) ($row['image'] ?? '');
        $model->delete($id);

        if ($this->isStoredNewsFeaturedPath($img)) {
            $this->deleteNewsFeaturedFile($img);
        }

        cleanup_unused_editor_uploads();

        return redirect()->to(base_url('admin/konten/berita'))->with('message', 'Berita berhasil dihapus.');
    }

    /**
     * @return array<string, string>
     */
    private function textValidationRules(): array
    {
        return [
            'title'   => 'required|max_length[255]',
            'excerpt' => 'permit_empty|max_length[2000]',
            'author'  => 'permit_empty|max_length[120]',
            'content' => 'permit_empty|max_length[600000]',
            'status'  => 'required|in_list[draft,publish]',
        ];
    }

    /**
     * @return array<string, int|string|null>
     */
    private function articlePayloadFromRequest(): array
    {
        $status = (string) $this->request->getPost('status');

        return [
            'title'        => (string) $this->request->getPost('title'),
            'excerpt'      => trim((string) $this->request->getPost('excerpt')) ?: null,
            'author'       => trim((string) $this->request->getPost('author')) ?: null,
            'content'      => safe_admin_html((string) $this->request->getPost('content')),
            'is_published' => $status === 'publish' ? 1 : 0,
            'is_exclusive' => (int) $this->request->getPost('is_exclusive'),
        ];
    }

    /**
     * Unggah file baru jika ada; jika tidak, pada update gunakan gambar sebelumnya.
     *
     * @return non-falsy-string|null path relatif disimpan di DB, atau null jika gagal
     */
    private function resolveFeaturedUpload(bool $isCreate, ?string $previousStored): ?string
    {
        $file = $this->request->getFile('featured_image');
        if ($file !== null && $file->isValid() && ! $file->hasMoved()) {
            $stored = $this->storeFeaturedFile($file);
            if ($stored !== null) {
                return $stored;
            }

            return $isCreate ? null : ($previousStored !== '' ? $previousStored : null);
        }

        if ($isCreate) {
            return null;
        }

        $current = trim((string) $this->request->getPost('current_image'));
        if ($current !== '') {
            return $current;
        }

        return $previousStored !== '' ? $previousStored : null;
    }

    private function storeFeaturedFile(object $file): ?string
    {
        $ext = strtolower($file->getClientExtension() ?: '');
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (! in_array($ext, $allowedExt, true)) {
            return null;
        }

        $sizeBytes = (int) $file->getSize();
        if ($sizeBytes <= 0 || $sizeBytes > 5 * 1024 * 1024) {
            return null;
        }

        $mime = strtolower($file->getMimeType() ?? '');
        $allowedMime = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ];
        if ($mime === '' || ! in_array($mime, $allowedMime, true)) {
            return null;
        }

        $targetDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'news';
        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            return null;
        }

        $newName = $file->getRandomName();
        $file->move($targetDir, $newName);

        return 'uploads/news/' . $newName;
    }

    private function isStoredNewsFeaturedPath(string $stored): bool
    {
        $stored = trim($stored);
        if ($stored === '' || preg_match('#^https?://#i', $stored) === 1) {
            return false;
        }

        return str_starts_with(ltrim($stored, '/'), 'uploads/news/');
    }

    private function deleteNewsFeaturedFile(string $stored): void
    {
        if (! $this->isStoredNewsFeaturedPath($stored)) {
            return;
        }

        $rel = ltrim($stored, '/');
        $path = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
