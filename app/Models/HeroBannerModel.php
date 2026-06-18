<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class HeroBannerModel extends Model
{
    protected $table            = 'hero_banners';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public static function tableReady(): bool
    {
        try {
            return Database::connect()->tableExists('hero_banners');
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
     * @return array<int, array<string, mixed>>
     */
    public function getActiveForHero(): array
    {
        $rows = $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'id'    => (int) ($row['id'] ?? 0),
                'title' => (string) ($row['title'] ?? ''),
                'image' => self::publicImageUrl((string) ($row['image'] ?? '')),
            ];
        }

        return $out;
    }
}
