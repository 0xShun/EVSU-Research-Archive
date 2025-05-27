<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class College extends Seeder
{
    public function run()
    {
        // Clear existing data
        $this->db->table('colleges')->truncate();

        $data = [
            [
                'name' => 'College of Architecture and Allied Discipline',
                'short_name' => 'CAAD'
            ],
            [
                'name' => 'College of Arts and Sciences',
                'short_name' => 'CAS'
            ],
            [
                'name' => 'College of Business and Entrepreneurship',
                'short_name' => 'CBE'
            ],
            [
                'name' => 'College of Education',
                'short_name' => 'COE'
            ],
            [
                'name' => 'College of Engineering',
                'short_name' => 'CENG'
            ],
            [
                'name' => 'College of Technology',
                'short_name' => 'COT'
            ]
        ];

        $this->db->table('colleges')->insertBatch($data);
    }
}
