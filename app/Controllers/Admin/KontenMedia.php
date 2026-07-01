<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class KontenMedia extends BaseController
{
    public function uploadImage(): ResponseInterface
    {
        $file = $this->request->getFile('file');
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'File gambar tidak valid.',
            ]);
        }

        $ext = strtolower($file->getClientExtension() ?: '');
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (! in_array($ext, $allowedExt, true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'error' => 'Format gambar tidak didukung.',
            ]);
        }

        $sizeBytes = (int) $file->getSize();
        if ($sizeBytes <= 0 || $sizeBytes > 5 * 1024 * 1024) {
            return $this->response->setStatusCode(422)->setJSON([
                'error' => 'Ukuran gambar maksimal 5MB.',
            ]);
        }

        $mime = strtolower($file->getMimeType() ?? '');
        $allowedMime = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ];
        if ($mime === '' || ! in_array($mime, $allowedMime, true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'error' => 'MIME type gambar tidak valid.',
            ]);
        }

        $targetDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'editor';
        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Gagal menyiapkan direktori upload.',
            ]);
        }

        $newName = $file->getRandomName();
        $file->move($targetDir, $newName);

        return $this->response->setJSON([
            'location' => '/uploads/editor/' . $newName,
        ]);
    }

    /**
     * Hapus satu file gambar dari folder uploads/editor.
     * Hanya menghapus jika file sudah tidak dipakai di konten manapun.
     */
    public function deleteImage(): ResponseInterface
    {
        helper('content');

        $src = trim((string) ($this->request->getJSON(true)['src'] ?? ''));

        if ($src === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'Parameter src tidak boleh kosong.',
            ]);
        }

        // Ambil nama file dari URL/path
        $filename = basename(parse_url($src, PHP_URL_PATH) ?? '');

        if (! preg_match('/^[a-zA-Z0-9._-]+\.(png|jpe?g|webp|gif)$/i', $filename)) {
            return $this->response->setStatusCode(422)->setJSON([
                'error' => 'Nama file tidak valid.',
            ]);
        }

        $result = delete_editor_upload_file($filename);

        if ($result) {
            return $this->response->setJSON([
                'deleted'   => true,
                'filename'  => $filename,
            ]);
        }

        // File masih dipakai di konten lain — bukan error, cukup skip
        return $this->response->setJSON([
            'deleted'  => false,
            'filename' => $filename,
            'reason'   => 'File masih digunakan di konten lain.',
        ]);
    }
}