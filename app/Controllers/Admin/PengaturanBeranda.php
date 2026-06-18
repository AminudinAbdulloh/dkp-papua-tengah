<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SitePageModel;
use CodeIgniter\HTTP\ResponseInterface;

class PengaturanBeranda extends BaseController
{
    public function index(): string
    {
        $model = model(SitePageModel::class);

        $data = [
            'title' => 'Pengaturan Latar Belakang Beranda',
            'adminNav' => 'pengaturan-beranda',
            'setting' => $model->findBySlug(SitePageModel::SLUG_PENGATURAN_BERANDA),
        ];

        return view('admin/pengaturan_beranda/index', $data);
    }

    public function update(): ResponseInterface
    {
        $rules = [
            'hero_bg' => 'permit_empty|uploaded[hero_bg]|is_image[hero_bg]|mime_in[hero_bg,image/jpg,image/jpeg,image/png,image/webp]|max_size[hero_bg,4096]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->handleUpload('hero_bg', SitePageModel::SLUG_PENGATURAN_BERANDA, 'Pengaturan Beranda', 'Pengaturan halaman utama');

        return redirect()->to(base_url('admin/pengaturan-beranda'))
            ->with('success', 'Pengaturan latar belakang beranda berhasil disimpan.');
    }

    private function handleUpload(string $inputName, string $slug, string $title, string $description): void
    {
        $model = model(SitePageModel::class);
        $setting = $model->findBySlug($slug);
        
        $imageFile = $this->request->getFile($inputName);
        $bodyVal = $setting['body'] ?? '';

        if ($imageFile !== null && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'uploads/hero', $newName);
            
            // Delete old file if exists
            if (!empty($setting['body']) && file_exists(FCPATH . $setting['body'])) {
                @unlink(FCPATH . $setting['body']);
            }
            
            $bodyVal = 'uploads/hero/' . $newName;
        }

        if ($setting === null) {
            $model->insert([
                'slug' => $slug,
                'title' => $title,
                'description' => $description,
                'body' => $bodyVal,
            ]);
        } else {
            $model->update($setting['id'], [
                'body' => $bodyVal,
            ]);
        }
    }
}
