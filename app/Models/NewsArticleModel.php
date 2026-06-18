<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class NewsArticleModel extends Model
{
    protected $table            = 'news_articles';
    protected $primaryKey     = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'excerpt',
        'image',
        'author',
        'views',
        'likes',
        'dislikes',
        'content',
        'is_published',
        'is_exclusive',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public static function tableReady(): bool
    {
        try {
            return Database::connect()->tableExists('news_articles');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * URL untuk atribut src gambar (path relatif atau URL absolut).
     */
    public static function publicImageUrl(string $stored): string
    {
        $stored = trim($stored);
        if ($stored === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $stored) === 1) {
            return $stored;
        }

        return base_url(ltrim($stored, '/'));
    }

    /**
     * Menambah hitungan tayangan pembaca.
     * Akan bertambah setiap kali halaman dimuat, kecuali oleh bot.
     */
    public function recordReaderVisitIfNewSession(int $id): void
    {
        $request = \Config\Services::request();
        if ($request instanceof \CodeIgniter\HTTP\IncomingRequest) {
            $agent = $request->getUserAgent();
            if ($agent->isRobot()) {
                return;
            }
        }

        $row = $this->where('id', $id)->where('is_published', 1)->first();
        if ($row === null) {
            return;
        }

        $this->db->table($this->table)->where('id', $id)->increment('views', 1);
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    public function rowToPublicShape(array $row): array
    {
        $views = (int) ($row['views'] ?? 0);
        $created = (string) ($row['created_at'] ?? '');
        $dateKey = '';
        if ($created !== '' && preg_match('/^(\d{4}-\d{2}-\d{2})/', $created, $m) === 1) {
            $dateKey = $m[1];
        }

        return [
            'id'           => (int) $row['id'],
            'date'         => self::formatIndonesianDate($dateKey),
            'title'        => (string) ($row['title'] ?? ''),
            'excerpt'      => (string) ($row['excerpt'] ?? ''),
            'image'        => self::publicImageUrl((string) ($row['image'] ?? '')),
            'author'       => (string) ($row['author'] ?? 'Admin'),
            'views'        => number_format($views, 0, ',', '.'),
            'likes'        => (int) ($row['likes'] ?? 0),
            'dislikes'     => (int) ($row['dislikes'] ?? 0),
            'content'      => (string) ($row['content'] ?? ''),
            'is_exclusive' => (int) ($row['is_exclusive'] ?? 0),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getPublishedForPublic(int $limit = 9): array
    {
        $rows = $this->where('is_published', 1)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($limit, 'public');

        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->rowToPublicShape($row);
        }

        return $out;
    }

    /**
     * Mengambil berita terbit yang ditandai eksklusif.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getExclusiveNews(int $limit = 5): array
    {
        $rows = $this->where('is_published', 1)
            ->where('is_exclusive', 1)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll($limit);

        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->rowToPublicShape($row);
        }

        return $out;
    }

    public function getPublishedById(int $id): ?array
    {
        $row = $this->where('id', $id)->where('is_published', 1)->first();

        return $row !== null ? $this->rowToPublicShape($row) : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getPopularPublished(?int $excludeId = null, int $limit = 4): array
    {
        $builder = $this->where('is_published', 1);
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        $rows = $builder->orderBy('views', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll(50);

        $shaped = [];
        foreach ($rows as $row) {
            $shaped[] = $this->rowToPublicShape($row);
        }

        usort($shaped, static function (array $a, array $b): int {
            $viewsA = (int) preg_replace('/\D+/', '', (string) ($a['views'] ?? '0'));
            $viewsB = (int) preg_replace('/\D+/', '', (string) ($b['views'] ?? '0'));

            return $viewsB <=> $viewsA;
        });

        return array_slice($shaped, 0, $limit);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAllForAdmin(int $limit = 10): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($limit, 'admin');
    }

    public static function formatIndonesianDate(string $ymd): string
    {
        $ymd = trim($ymd);
        if ($ymd === '' || preg_match('/^\d{4}-\d{2}-\d{2}$/', $ymd) !== 1) {
            return '';
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $ts = strtotime($ymd . ' 12:00:00');
        if ($ts === false) {
            return '';
        }

        $d = (int) date('j', $ts);
        $m = (int) date('n', $ts);
        $y = (int) date('Y', $ts);

        return $d . ' ' . ($months[$m] ?? '') . ' ' . $y;
    }

    /**
     * Label tanggal publik dari baris admin (created_at).
     */
    public static function displayDateFromRow(array $row): string
    {
        $created = (string) ($row['created_at'] ?? '');
        if ($created !== '' && preg_match('/^(\d{4}-\d{2}-\d{2})/', $created, $m) === 1) {
            return self::formatIndonesianDate($m[1]);
        }

        return '';
    }
}
