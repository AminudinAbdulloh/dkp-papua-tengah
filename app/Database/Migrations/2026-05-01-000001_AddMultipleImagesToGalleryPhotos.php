<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMultipleImagesToGalleryPhotos extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('gallery_photos', [
            'image_2' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
                'null'       => true,
                'default'    => null,
                'after'      => 'image',
            ],
            'image_3' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
                'null'       => true,
                'default'    => null,
                'after'      => 'image_2',
            ],
            'image_4' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
                'null'       => true,
                'default'    => null,
                'after'      => 'image_3',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('gallery_photos', ['image_2', 'image_3', 'image_4']);
    }
}
