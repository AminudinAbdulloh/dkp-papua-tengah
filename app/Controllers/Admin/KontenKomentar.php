<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NewsCommentModel;
use CodeIgniter\HTTP\ResponseInterface;

class KontenKomentar extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function index(?int $newsId = null): ResponseInterface|string
    {
        if ($newsId === null) {
            $newsIdRaw = $this->request->getGet('news_id');
            $newsId = $newsIdRaw !== null && $newsIdRaw !== '' ? (int) $newsIdRaw : null;
        }

        if ($newsId === null) {
            return redirect()->to(base_url('admin/konten/berita'))->with('error', 'Silakan pilih berita terlebih dahulu untuk mengelola komentar.');
        }

        $newsModel = model(\App\Models\NewsArticleModel::class);
        $news = $newsModel->find($newsId);
        if ($news === null) {
            return redirect()->to(base_url('admin/konten/berita'))->with('error', 'Berita tidak ditemukan.');
        }

        $newsTitle = $news['title'];

        $model = model(NewsCommentModel::class);
        
        $q = (string) $this->request->getGet('q');
        $status = (string) $this->request->getGet('status');

        $rows = $model->getCommentsWithNews($q, $status, 10, $newsId);

        return view('admin/konten/komentar_index', [
            'title'       => 'Kelola Komentar Berita',
            'adminNav'    => 'konten-berita',
            'comments'    => $rows,
            'pager'       => $model->pager,
            'searchQuery' => $q,
            'statusFilter'=> $status,
            'newsId'      => $newsId,
            'newsTitle'   => $newsTitle,
        ]);
    }

    public function approve(int $id): ResponseInterface
    {
        $model = model(NewsCommentModel::class);
        $comment = $model->find($id);
        
        $newsId = $this->request->getVar('news_id');
        $fallbackUrl = base_url('admin/konten/berita');
        $redirectUrl = $newsId !== null && $newsId !== '' ? base_url('admin/konten/komentar/' . (int)$newsId) : $fallbackUrl;

        if ($comment === null) {
            return redirect()->to($redirectUrl)->with('error', 'Komentar tidak ditemukan.');
        }

        $model->update($id, ['status' => 'approved']);

        return redirect()->to($redirectUrl)->with('message', 'Komentar berhasil disetujui.');
    }

    public function reject(int $id): ResponseInterface
    {
        $model = model(NewsCommentModel::class);
        $comment = $model->find($id);
        
        $newsId = $this->request->getVar('news_id');
        $fallbackUrl = base_url('admin/konten/berita');
        $redirectUrl = $newsId !== null && $newsId !== '' ? base_url('admin/konten/komentar/' . (int)$newsId) : $fallbackUrl;

        if ($comment === null) {
            return redirect()->to($redirectUrl)->with('error', 'Komentar tidak ditemukan.');
        }

        $model->update($id, ['status' => 'rejected']);

        return redirect()->to($redirectUrl)->with('message', 'Komentar berhasil ditolak.');
    }

    public function delete(int $id): ResponseInterface
    {
        $model = model(NewsCommentModel::class);
        $comment = $model->find($id);
        
        $newsId = $this->request->getVar('news_id');
        $fallbackUrl = base_url('admin/konten/berita');
        $redirectUrl = $newsId !== null && $newsId !== '' ? base_url('admin/konten/komentar/' . (int)$newsId) : $fallbackUrl;

        if ($comment === null) {
            return redirect()->to($redirectUrl)->with('error', 'Komentar tidak ditemukan.');
        }

        $model->delete($id);

        return redirect()->to($redirectUrl)->with('message', 'Komentar berhasil dihapus.');
    }
}
