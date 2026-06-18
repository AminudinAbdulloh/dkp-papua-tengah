<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateVisitorsTable extends Migration
{
    public function up()
    {
        // 1. Hapus tabel page_views
        $this->forge->dropTable('page_views', true);

        // 2. Persiapkan kolom yang akan ditambahkan ke tabel visitors
        $fieldsToAdd = [
            'cookie_token' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'user_agent',
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
        ];

        // Jika ip_address tidak ada di tabel visitors, tambahkan juga
        if (!$this->db->fieldExists('ip_address', 'visitors')) {
            $fieldsToAdd['ip_address'] = [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'after'      => 'id',
            ];
        }

        $this->forge->addColumn('visitors', $fieldsToAdd);
    }

    public function down()
    {
        // 1. Hapus kolom-kolom baru dari tabel visitors
        $columnsToDrop = ['cookie_token', 'today_views', 'total_views', 'updated_at'];
        if ($this->db->fieldExists('ip_address', 'visitors')) {
            $columnsToDrop[] = 'ip_address';
        }
        $this->forge->dropColumn('visitors', $columnsToDrop);

        // 2. Buat kembali tabel page_views
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
