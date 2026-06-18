<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsExclusiveToNews extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('news_articles', [
            'is_exclusive' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'is_published',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('news_articles', ['is_exclusive']);
    }
}
