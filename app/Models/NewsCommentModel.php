<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class NewsCommentModel extends Model
{
    protected $table            = 'news_comments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'news_id',
        'name',
        'comment',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil komentar yang disetujui untuk berita tertentu.
     */
    public function getApprovedComments(int $newsId): array
    {
        return $this->where('news_id', $newsId)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Mengambil daftar komentar dengan data artikel beritanya untuk panel admin.
     */
    public function getCommentsWithNews(string $search = '', string $status = '', int $limit = 10, ?int $newsId = null): array
    {
        $builder = $this->select('news_comments.*, news_articles.title as news_title')
                        ->join('news_articles', 'news_articles.id = news_comments.news_id', 'left');

        if ($newsId !== null) {
            $builder->where('news_comments.news_id', $newsId);
        }

        if ($search !== '') {
            $builder->groupStart()
                    ->like('news_comments.name', $search)
                    ->orLike('news_comments.comment', $search)
                    ->orLike('news_articles.title', $search)
                    ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('news_comments.status', $status);
        }

        return $builder->orderBy('news_comments.created_at', 'DESC')
                       ->paginate($limit, 'admin');
    }
}
