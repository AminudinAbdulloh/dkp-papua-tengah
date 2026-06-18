<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SitePageModel;
use CodeIgniter\HTTP\ResponseInterface;

class PengaturanHeader extends BaseController
{
    public function index(): string
    {
        $model = model(SitePageModel::class);

        $data = [
            'title'           => 'Pengaturan Latar Belakang Header',
            'adminNav'        => 'pengaturan-header',
            'headerProfil'    => $model->findBySlug(SitePageModel::SLUG_PENGATURAN_HEADER_PROFIL),
            'headerInformasi' => $model->findBySlug(SitePageModel::SLUG_PENGATURAN_HEADER_INFORMASI),
            'headerGaleri'    => $model->findBySlug(SitePageModel::SLUG_PENGATURAN_HEADER_GALERI),
            'headerPpid'      => $model->findBySlug(SitePageModel::SLUG_PENGATURAN_HEADER_PPID),
        ];

        return view('admin/pengaturan_header/index', $data);
    }

    public function update(): ResponseInterface
    {
        $rules = [
            'header_profil'    => 'permit_empty|uploaded[header_profil]|is_image[header_profil]|mime_in[header_profil,image/jpg,image/jpeg,image/png,image/webp]|max_size[header_profil,4096]',
            'header_informasi' => 'permit_empty|uploaded[header_informasi]|is_image[header_informasi]|mime_in[header_informasi,image/jpg,image/jpeg,image/png,image/webp]|max_size[header_informasi,4096]',
            'header_galeri'    => 'permit_empty|uploaded[header_galeri]|is_image[header_galeri]|mime_in[header_galeri,image/jpg,image/jpeg,image/png,image/webp]|max_size[header_galeri,4096]',
            'header_ppid'      => 'permit_empty|uploaded[header_ppid]|is_image[header_ppid]|mime_in[header_ppid,image/jpg,image/jpeg,image/png,image/webp]|max_size[header_ppid,4096]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->handleUpload('header_profil', SitePageModel::SLUG_PENGATURAN_HEADER_PROFIL, 'Header Profil', 'Latar belakang header halaman profil');
        $this->handleUpload('header_informasi', SitePageModel::SLUG_PENGATURAN_HEADER_INFORMASI, 'Header Informasi', 'Latar belakang header halaman berita dan pengumuman');
        $this->handleUpload('header_galeri', SitePageModel::SLUG_PENGATURAN_HEADER_GALERI, 'Header Galeri', 'Latar belakang header halaman foto dan video');
        $this->handleUpload('header_ppid', SitePageModel::SLUG_PENGATURAN_HEADER_PPID, 'Header PPID', 'Latar belakang header halaman publikasi, layanan, informasi publik, dan download');

        return redirect()->to(base_url('admin/pengaturan-header'))
            ->with('success', 'Pengaturan latar belakang header berhasil disimpan.');
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
                'slug'        => $slug,
                'title'       => $title,
                'description' => $description,
                'body'        => $bodyVal,
            ]);
        } else {
            $model->update($setting['id'], [
                'body' => $bodyVal,
            ]);
        }
    }
}
