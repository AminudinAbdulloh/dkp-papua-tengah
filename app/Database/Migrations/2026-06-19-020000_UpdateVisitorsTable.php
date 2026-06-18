<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateVisitorsTable extends Migration
{
    public function up()
    {
        // 1. Hapus tabel page_views
        $this->forge->dropTable('page_views', true);

        // 2. Tambahkan kolom baru ke tabel visitors
        $this->forge->addColumn('visitors', [
            'cookie_token' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'id',
            ],
            'today_views' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'cookie_token',
            ],
            'total_views' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'today_views',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'total_views',
            ],
        ]);

        // 3. Hapus kolom ip_address dan user_agent jika ada di tabel visitors
        $columnsToDrop = [];
        if ($this->db->fieldExists('ip_address', 'visitors')) {
            $columnsToDrop[] = 'ip_address';
        }
        if ($this->db->fieldExists('user_agent', 'visitors')) {
            $columnsToDrop[] = 'user_agent';
        }
        if (!empty($columnsToDrop)) {
            $this->forge->dropColumn('visitors', $columnsToDrop);
        }
    }

    public function down()
    {
        // 1. Hapus kolom-kolom baru dari tabel visitors
        $this->forge->dropColumn('visitors', ['cookie_token', 'today_views', 'total_views', 'updated_at']);

        // 2. Tambahkan kembali kolom ip_address dan user_agent
        $this->forge->addColumn('visitors', [
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'after'      => 'id',
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'after'      => 'ip_address',
            ],
        ]);

        // 3. Buat kembali tabel page_views
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'url' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('page_views');
    }
}
