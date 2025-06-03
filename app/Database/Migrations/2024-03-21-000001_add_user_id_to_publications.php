<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserIdToPublications extends Migration
{
    public function up()
    {
        $this->forge->addColumn('publications', [
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'id',
            ],
        ]);

        // Add foreign key constraint
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        // Remove foreign key first
        $this->forge->dropForeignKey('publications', 'publications_user_id_foreign');
        
        // Then remove the column
        $this->forge->dropColumn('publications', 'user_id');
    }
} 