<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropDescriptionFromDownloads extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('downloads', 'description');
    }

    public function down()
    {
        $this->forge->addColumn('downloads', [
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }
}
