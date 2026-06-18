<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropLinkUrlFromHeroBanners extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('hero_banners')) {
            return;
        }

        if ($this->db->fieldExists('link_url', 'hero_banners')) {
            $this->forge->dropColumn('hero_banners', 'link_url');
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('hero_banners')) {
            return;
        }

        if (! $this->db->fieldExists('link_url', 'hero_banners')) {
            $this->forge->addColumn('hero_banners', [
                'link_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 512,
                    'null'       => true,
                    'after'      => 'image',
                ],
            ]);
        }
    }
}
