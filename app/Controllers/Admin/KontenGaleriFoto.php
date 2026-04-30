<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GalleryPhotoModel;
use CodeIgniter\HTTP\ResponseInterface;

class KontenGaleriFoto extends BaseController
{
    protected $helpers = ['form', 'url'];

    /** Kolom gambar yang tersedia (urut) */
    private const IMG_COLS = ['image', 'image_2', 'image_3', 'image_4'];
    private const MAX_PHOTOS = 4;

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
            'title'      => 'Tambah Galeri Foto',
            'adminNav'   => 'konten-galeri-foto',
            'photo'      => null,
            'formAction' => base_url('admin/konten/galeri-foto/simpan'),
        ]);
    }

    public function store(): ResponseInterface
    {
        if (! $this->validate(['title' => 'required|max_length[255]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $files = $this->request->getFileMultiple('gallery_images');
        if (empty($files) || ! $this->hasAnyValidFile($files)) {
            return redirect()->back()->withInput()
                ->with('errors', ['gallery_images' => 'Pilih minimal satu file gambar.']);
        }

        $stored = [];
        $errors = [];
        foreach (array_slice($files, 0, self::MAX_PHOTOS) as $i => $file) {
            if ($file === null || ! $file->isValid() || $file->hasMoved()) continue;
            $path = $this->storeGalleryFile($file);
            if ($path === null) {
                $errors[] = 'File #' . ($i + 1) . ' (' . esc($file->getClientName()) . ') gagal — pastikan JPG/PNG/WebP/GIF, maks. 5MB.';
            } else {
                $stored[] = $path;
            }
        }

        if (empty($stored)) {
            return redirect()->back()->withInput()
                ->with('errors', array_merge(['gallery_images' => 'Tidak ada file yang berhasil diunggah.'], $errors));
        }

        $data = ['title' => (string) $this->request->getPost('title')];
        foreach (self::IMG_COLS as $i => $col) {
            $data[$col] = $stored[$i] ?? null;
        }

        model(GalleryPhotoModel::class)->insert($data);

        $msg = count($stored) . ' foto berhasil ditambahkan ke galeri.';
        if ($errors) $msg .= ' ' . implode(' | ', $errors);

        return redirect()->to(base_url('admin/konten/galeri-foto'))->with('message', $msg);
    }

    public function edit(int $id): ResponseInterface|string
    {
        $row = model(GalleryPhotoModel::class)->find($id);
        if ($row === null) {
            return redirect()->to(base_url('admin/konten/galeri-foto'))->with('error', 'Data tidak ditemukan.');
        }

        return view('admin/konten/galeri_foto_form', [
            'title'      => 'Edit Galeri Foto',
            'adminNav'   => 'konten-galeri-foto',
            'photo'      => $row,
            'formAction' => base_url('admin/konten/galeri-foto/' . $id . '/update'),
        ]);
    }

    public function update(int $id): ResponseInterface
    {
        $model    = model(GalleryPhotoModel::class);
        $existing = $model->find($id);
        if ($existing === null) {
            return redirect()->to(base_url('admin/konten/galeri-foto'))->with('error', 'Data tidak ditemukan.');
        }

        if (! $this->validate(['title' => 'required|max_length[255]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = ['title' => (string) $this->request->getPost('title')];
        $errors     = [];

        foreach (self::IMG_COLS as $i => $col) {
            $fileKey = 'gallery_image_' . ($i + 1);
            $file    = $this->request->getFile($fileKey);

            if ($file !== null && $file->isValid() && ! $file->hasMoved()) {
                // File baru diunggah — simpan dan hapus lama
                $newPath = $this->storeGalleryFile($file);
                if ($newPath !== null) {
                    $old = (string) ($existing[$col] ?? '');
                    if ($old !== '' && $this->isStoredGalleryPath($old)) {
                        $this->deleteGalleryFile($old);
                    }
                    $updateData[$col] = $newPath;
                } else {
                    $errors[] = 'Foto ' . ($i + 1) . ' gagal — pastikan JPG/PNG/WebP/GIF, maks. 5MB.';
                    // Pertahankan gambar lama
                    $updateData[$col] = $existing[$col] ?? null;
                }
            } else {
                // Cek apakah slot ini di-clear
                $clear = $this->request->getPost('clear_image_' . ($i + 1));
                if ($clear === '1') {
                    $old = (string) ($existing[$col] ?? '');
                    if ($old !== '' && $this->isStoredGalleryPath($old)) {
                        $this->deleteGalleryFile($old);
                    }
                    $updateData[$col] = null;
                } else {
                    $updateData[$col] = $existing[$col] ?? null;
                }
            }
        }

        // Pastikan image (slot 1) tidak kosong jika ada slot lain terisi
        $model->update($id, $updateData);

        $msg = 'Galeri foto berhasil diperbarui.';
        if ($errors) $msg .= ' Peringatan: ' . implode(' | ', $errors);

        return redirect()->to(base_url('admin/konten/galeri-foto'))->with('message', $msg);
    }

    public function delete(int $id): ResponseInterface
    {
        $model = model(GalleryPhotoModel::class);
        $row   = $model->find($id);
        if ($row === null) {
            return redirect()->to(base_url('admin/konten/galeri-foto'))->with('error', 'Data tidak ditemukan.');
        }

        $model->delete($id);

        foreach (self::IMG_COLS as $col) {
            $img = (string) ($row[$col] ?? '');
            if ($img !== '' && $this->isStoredGalleryPath($img)) {
                $this->deleteGalleryFile($img);
            }
        }

        return redirect()->to(base_url('admin/konten/galeri-foto'))->with('message', 'Galeri foto berhasil dihapus.');
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function hasAnyValidFile(array $files): bool
    {
        foreach ($files as $f) {
            if ($f !== null && $f->isValid() && ! $f->hasMoved()) return true;
        }
        return false;
    }

    private function storeGalleryFile(object $file): ?string
    {
        $ext = strtolower($file->getClientExtension() ?: '');
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) return null;

        $size = (int) $file->getSize();
        if ($size <= 0 || $size > 5 * 1024 * 1024) return null;

        $mime = strtolower($file->getMimeType() ?? '');
        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) return null;

        $dir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'gallery';
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) return null;

        $name = $file->getRandomName();
        $file->move($dir, $name);

        return 'uploads/gallery/' . $name;
    }

    private function isStoredGalleryPath(string $stored): bool
    {
        $stored = trim($stored);
        if ($stored === '' || preg_match('#^https?://#i', $stored) === 1) return false;
        return str_starts_with(ltrim($stored, '/'), 'uploads/gallery/');
    }

    private function deleteGalleryFile(string $stored): void
    {
        if (! $this->isStoredGalleryPath($stored)) return;
        $rel  = ltrim($stored, '/');
        $path = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
        if (is_file($path)) @unlink($path);
    }
}
