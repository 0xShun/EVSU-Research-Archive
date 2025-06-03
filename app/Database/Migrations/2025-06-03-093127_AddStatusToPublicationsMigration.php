<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToPublicationsMigration extends Migration
{
    public function up()
    {
        $this->forge->addColumn('publications', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
                'after' => 'file_path',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('publications', 'status');
    }
}
