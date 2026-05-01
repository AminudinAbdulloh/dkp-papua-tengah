<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDownloadsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'file_size' => [
                'type'       => 'BIGINT',
                'unsigned'   => true,
            ],
            'file_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'download_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('downloads');
    }

    public function down()
    {
        $this->forge->dropTable('downloads');
    }
}
