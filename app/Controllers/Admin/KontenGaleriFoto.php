<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GalleryPhotoModel;
use CodeIgniter\HTTP\ResponseInterface;

class KontenGaleriFoto extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index(): string
    {
        $model = model(GalleryPhotoModel::class);
        $rows = $model->getAllForAdmin();

        return view('admin/konten/galeri_foto_index', [
            'title'    => 'Galeri Foto',
            'adminNav' => 'konten-galeri-foto',
            'photos'   => $rows,
            'pager'    => $model->pager,
        ]);
    }

    public function create(): string
    {
        return view('admin/konten/galeri_foto_form', [
            'title'      => 'Tambah Foto Galeri',
            'adminNav'   => 'konten-galeri-foto',
            'photo'      => null,
            'formAction' => base_url('admin/konten/galeri-foto/simpan'),
        ]);
    }

    public function store(): ResponseInterface
    {
        // Validasi hanya field title (gambar divalidasi per-file di bawah)
        if (! $this->validate(['title' => 'permit_empty|max_length[255]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $titleBase  = trim((string) $this->request->getPost('title'));
        $files      = $this->request->getFileMultiple('gallery_image');
        $model      = model(GalleryPhotoModel::class);

        if (empty($files)) {
            return redirect()->back()->withInput()->with('errors', ['gallery_image' => 'Pilih minimal satu file gambar.']);
        }

        $saved  = 0;
        $errors = [];

        foreach ($files as $index => $file) {
            if ($file === null || ! $file->isValid() || $file->hasMoved()) {
                continue;
            }

            $path = $this->storeGalleryFile($file);
            if ($path === null) {
                $errors[] = 'File #' . ($index + 1) . ' (' . esc($file->getClientName()) . ') gagal disimpan — pastikan format JPG/PNG/WebP/GIF dan ukuran maks. 5MB.';
                continue;
            }

            // Judul: gunakan titleBase jika diisi, fallback ke nama file tanpa ekstensi
            $title = $titleBase !== ''
                ? ($saved > 0 ? $titleBase . ' ' . ($saved + 1) : $titleBase)
                : pathinfo($file->getClientName(), PATHINFO_FILENAME);

            $model->insert([
                'title' => $title,
                'image' => $path,
            ]);
            $saved++;
        }

        if ($saved === 0) {
            return redirect()->back()->withInput()->with('errors', array_merge(
                ['gallery_image' => 'Tidak ada file yang berhasil diunggah.'],
                $errors
            ));
        }

        $msg = $saved === 1
            ? 'Foto galeri berhasil ditambahkan.'
            : $saved . ' foto galeri berhasil ditambahkan.';

        if ($errors !== []) {
            $msg .= ' ' . count($errors) . ' file gagal: ' . implode(' | ', $errors);
        }

        return redirect()->to(base_url('admin/konten/galeri-foto'))->with('message', $msg);
    }


    public function edit(int $id): ResponseInterface|string
    {
        $row = model(GalleryPhotoModel::class)->find($id);
        if ($row === null) {
            return redirect()->to(base_url('admin/konten/galeri-foto'))->with('error', 'Foto tidak ditemukan.');
        }

        return view('admin/konten/galeri_foto_form', [
            'title'      => 'Edit Foto Galeri',
            'adminNav'   => 'konten-galeri-foto',
            'photo'      => $row,
            'formAction' => base_url('admin/konten/galeri-foto/' . $id . '/update'),
        ]);
    }

    public function update(int $id): ResponseInterface
    {
        $model = model(GalleryPhotoModel::class);
        $existing = $model->find($id);
        if ($existing === null) {
            return redirect()->to(base_url('admin/konten/galeri-foto'))->with('error', 'Foto tidak ditemukan.');
        }

        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $previous = (string) ($existing['image'] ?? '');
        $newPath = $this->resolveImageUpload(false, $previous);
        if ($newPath === null) {
            return redirect()->back()->withInput()->with('errors', ['gallery_image' => 'Gambar tidak valid. Unggah file baru atau pertahankan gambar saat ini.']);
        }

        $model->update($id, [
            'title' => (string) $this->request->getPost('title'),
            'image' => $newPath,
        ]);

        if ($newPath !== $previous && $this->isStoredGalleryPath($previous)) {
            $this->deleteGalleryFile($previous);
        }

        return redirect()->to(base_url('admin/konten/galeri-foto'))->with('message', 'Foto galeri berhasil diperbarui.');
    }

    public function delete(int $id): ResponseInterface
    {
        $model = model(GalleryPhotoModel::class);
        $row = $model->find($id);
        if ($row === null) {
            return redirect()->to(base_url('admin/konten/galeri-foto'))->with('error', 'Foto tidak ditemukan.');
        }

        $img = (string) ($row['image'] ?? '');
        $model->delete($id);

        if ($this->isStoredGalleryPath($img)) {
            $this->deleteGalleryFile($img);
        }

        return redirect()->to(base_url('admin/konten/galeri-foto'))->with('message', 'Foto galeri berhasil dihapus.');
    }

    /**
     * @return array<string, string>
     */
    private function validationRules(): array
    {
        return [
            'title' => 'permit_empty|max_length[255]',
        ];
    }

    /**
     * @return non-falsy-string|null
     */
    private function resolveImageUpload(bool $isCreate, ?string $previousStored): ?string
    {
        $file = $this->request->getFile('gallery_image');
        if ($file !== null && $file->isValid() && ! $file->hasMoved()) {
            $stored = $this->storeGalleryFile($file);
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

    private function storeGalleryFile(object $file): ?string
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

        $targetDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'gallery';
        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            return null;
        }

        $newName = $file->getRandomName();
        $file->move($targetDir, $newName);

        return 'uploads/gallery/' . $newName;
    }

    private function isStoredGalleryPath(string $stored): bool
    {
        $stored = trim($stored);
        if ($stored === '' || preg_match('#^https?://#i', $stored) === 1) {
            return false;
        }

        return str_starts_with(ltrim($stored, '/'), 'uploads/gallery/');
    }

    private function deleteGalleryFile(string $stored): void
    {
        if (! $this->isStoredGalleryPath($stored)) {
            return;
        }

        $rel = ltrim($stored, '/');
        $path = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
