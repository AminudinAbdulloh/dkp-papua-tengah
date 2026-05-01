<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DownloadModel;
use CodeIgniter\HTTP\ResponseInterface;

class KontenDownload extends BaseController
{
    protected $downloadModel;

    public function __construct()
    {
        $this->downloadModel = new DownloadModel();
    }

    public function index(): string
    {
        $search = $this->request->getGet('q');
        
        $query = $this->downloadModel;
        if ($search) {
            $query = $query->like('title', $search);
        }

        $data = [
            'adminNav'  => 'konten-download',
            'downloads' => $query->orderBy('created_at', 'DESC')->paginate(10, 'downloads'),
            'pager'     => $this->downloadModel->pager,
            'search'    => $search
        ];

        return view('admin/konten/download_index', $data);
    }

    public function create(): string
    {
        $data = [
            'adminNav' => 'konten-download',
            'title'    => 'Tambah Dokumen'
        ];

        return view('admin/konten/download_form', $data);
    }

    public function store(): ResponseInterface
    {
        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'file'        => 'uploaded[file]|max_size[file,20480]|ext_in[file,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('file');
        
        if ($file->isValid() && !$file->hasMoved()) {
            $fileSize = $file->getSize();
            $fileExt  = $file->getExtension();
            $newName  = $file->getRandomName();
            
            $file->move(FCPATH . 'uploads/downloads', $newName);

            $this->downloadModel->save([
                'title'       => $this->request->getPost('title'),
                'file_path'   => $newName,
                'file_size'   => $fileSize,
                'file_type'   => $fileExt,
            ]);

            return redirect()->to(base_url('admin/konten/download'))->with('message', 'Dokumen berhasil ditambahkan.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal mengunggah file.');
    }

    public function edit($id): string
    {
        $download = $this->downloadModel->find($id);
        if (!$download) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'adminNav' => 'konten-download',
            'title'    => 'Edit Dokumen',
            'download' => $download
        ];

        return view('admin/konten/download_form', $data);
    }

    public function update($id): ResponseInterface
    {
        $download = $this->downloadModel->find($id);
        if (!$download) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'file'        => 'permit_empty|max_size[file,20480]|ext_in[file,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id'          => $id,
            'title'       => $this->request->getPost('title'),
        ];

        $file = $this->request->getFile('file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileSize = $file->getSize();
            $fileExt  = $file->getExtension();
            $newName  = $file->getRandomName();

            $file->move(FCPATH . 'uploads/downloads', $newName);

            $data['file_path'] = $newName;
            $data['file_size'] = $fileSize;
            $data['file_type'] = $fileExt;
        }

        $this->downloadModel->save($data);

        return redirect()->to(base_url('admin/konten/download'))->with('message', 'Dokumen berhasil diperbarui.');
    }

    public function delete($id): ResponseInterface
    {
        $this->downloadModel->delete($id);
        return redirect()->to(base_url('admin/konten/download'))->with('message', 'Dokumen berhasil dihapus.');
    }
}
