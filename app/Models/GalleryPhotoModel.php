<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class GalleryPhotoModel extends Model
{
    protected $table            = 'gallery_photos';
    protected $primaryKey     = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'image',
        'image_2',
        'image_3',
        'image_4',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public static function tableReady(): bool
    {
        try {
            return Database::connect()->tableExists('gallery_photos');
        } catch (\Throwable) {
            return false;
        }
    }

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
     * @param array<string, mixed> $row
     * @return array<string, int|string>
     */
    public function rowToPublicShape(array $row): array
    {
        $created = (string) ($row['created_at'] ?? '');
        $dateKey = '';
        if ($created !== '' && preg_match('/^(\d{4}-\d{2}-\d{2})/', $created, $m) === 1) {
            $dateKey = $m[1];
        }

        // Kumpulkan semua slot gambar yang terisi
        $images = [];
        foreach (['image', 'image_2', 'image_3', 'image_4'] as $col) {
            $val = trim((string) ($row[$col] ?? ''));
            if ($val !== '') {
                $images[] = self::publicImageUrl($val);
            }
        }

        return [
            'id'       => (int) $row['id'],
            'image'    => $images[0] ?? '',          // thumbnail utama
            'images'   => $images,                   // semua foto
            'title'    => (string) ($row['title'] ?? ''),
            'date'     => NewsArticleModel::formatIndonesianDate($dateKey),
            'count'    => count($images),
        ];
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    public function getForPublic(int $limit = 9): array
    {
        $rows = $this->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($limit, 'public');

        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->rowToPublicShape($row);
        }

        return $out;
    }

    /**
     * @return array<string, int|string>|null
     */
    public function getByIdForPublic(int $id): ?array
    {
        $row = $this->find($id);

        return $row !== null ? $this->rowToPublicShape($row) : null;
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    public function getRelatedForPublic(int $excludeId, int $limit = 4): array
    {
        $rows = $this->where('id !=', $excludeId)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll($limit);

        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->rowToPublicShape($row);
        }

        return $out;
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

    public static function displayDateFromRow(array $row): string
    {
        $created = (string) ($row['created_at'] ?? '');
        if ($created !== '' && preg_match('/^(\d{4}-\d{2}-\d{2})/', $created, $m) === 1) {
            return NewsArticleModel::formatIndonesianDate($m[1]);
        }

        return '';
    }
}
