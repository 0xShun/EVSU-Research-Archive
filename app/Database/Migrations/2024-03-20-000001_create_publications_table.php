<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePublicationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'authors' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'abstract' => [
                'type' => 'TEXT',
            ],
            'keywords' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'department' => [
                'type' => 'ENUM',
                'constraint' => ['Computer Studies', 'Engineering', 'Education', 'Arts and Sciences'],
            ],
            'program' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'year' => [
                'type' => 'INT',
                'constraint' => 4,
            ],
            'publication_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['journal', 'conference', 'thesis', 'dissertation'],
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
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
        $this->forge->addKey('department');
        $this->forge->addKey('type');
        $this->forge->addKey('year');
        $this->forge->createTable('publications');
    }

    public function down()
    {
        $this->forge->dropTable('publications');
    }
} 