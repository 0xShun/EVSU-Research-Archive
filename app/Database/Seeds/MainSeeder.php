<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks temporarily
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        // Truncate tables in reverse order of dependencies
        $this->db->table('programs')->truncate();
        $this->db->table('departments')->truncate();
        $this->db->table('colleges')->truncate();

        // Re-enable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');

        // Run seeders in correct order
        $this->call('College');
        $this->call('Department');
        $this->call('Program');
    }
} 