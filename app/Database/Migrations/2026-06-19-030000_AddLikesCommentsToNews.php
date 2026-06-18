<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLikesCommentsToNews extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom likes dan dislikes ke tabel news_articles
        $this->forge->addColumn('news_articles', [
            'likes' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'views',
            ],
            'dislikes' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'likes',
            ],
        ]);

        // 2. Buat tabel news_comments
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'news_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'comment' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'pending',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('news_id');
        $this->forge->addKey('status');
        $this->forge->createTable('news_comments');
    }

    public function down(): void
    {
        // 1. Hapus tabel news_comments
        $this->forge->dropTable('news_comments', true);

        // 2. Hapus kolom likes dan dislikes dari tabel news_articles
        $this->forge->dropColumn('news_articles', ['likes', 'dislikes']);
    }
}
