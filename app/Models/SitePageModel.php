<?php

namespace App\Models;

use CodeIgniter\Model;

class SitePageModel extends Model
{
    public const SLUG_PROFIL_SEJARAH = 'profil/sejarah';
    public const SLUG_PROFIL_VISI_MISI = 'profil/visi-misi';
    public const SLUG_PROFIL_TUPOKSI = 'profil/tupoksi';
    public const SLUG_PROFIL_STRUKTUR = 'profil/struktur';
    public const SLUG_PROFIL_PEJABAT = 'profil/pejabat';
    public const SLUG_PROFIL_KONTAK = 'profil/kontak';
    public const SLUG_LAYANAN_ALUR_INFORMASI = 'layanan/alur-permohonan-informasi';
    public const SLUG_PENGATURAN_BERANDA = 'pengaturan/beranda';
    public const SLUG_PENGATURAN_HEADER_PROFIL = 'pengaturan/header-profil';
    public const SLUG_PENGATURAN_HEADER_INFORMASI = 'pengaturan/header-informasi';
    public const SLUG_PENGATURAN_HEADER_GALERI = 'pengaturan/header-galeri';
    public const SLUG_PENGATURAN_HEADER_PPID = 'pengaturan/header-ppid';

    protected $table            = 'site_pages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'slug',
        'title',
        'description',
        'body',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * @return array<string, mixed>|null
     */
    public function findBySlug(string $slug): ?array
    {
        $row = $this->where('slug', $slug)->first();

        return $row ?: null;
    }
}
