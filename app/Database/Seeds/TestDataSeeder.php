<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Seed Colleges
        $colleges = [
            [
                'name' => 'College of Engineering',
                'short_name' => 'COE',
                'description' => 'College of Engineering at EVSU'
            ],
            [
                'name' => 'College of Arts and Sciences',
                'short_name' => 'CAS',
                'description' => 'College of Arts and Sciences at EVSU'
            ]
        ];
        
        $this->db->table('colleges')->insertBatch($colleges);
        
        // Seed Departments
        $departments = [
            [
                'name' => 'Computer Engineering',
                'short_name' => 'CPE',
                'college_id' => 1,
                'description' => 'Department of Computer Engineering'
            ],
            [
                'name' => 'Electrical Engineering',
                'short_name' => 'EE',
                'college_id' => 1,
                'description' => 'Department of Electrical Engineering'
            ],
            [
                'name' => 'Mathematics',
                'short_name' => 'MATH',
                'college_id' => 2,
                'description' => 'Department of Mathematics'
            ]
        ];
        
        $this->db->table('departments')->insertBatch($departments);
        
        // Seed Programs
        $programs = [
            [
                'name' => 'Bachelor of Science in Computer Engineering',
                'short_name' => 'BSCpE',
                'department_id' => 1,
                'description' => 'BS Computer Engineering Program'
            ],
            [
                'name' => 'Bachelor of Science in Electrical Engineering',
                'short_name' => 'BSEE',
                'department_id' => 2,
                'description' => 'BS Electrical Engineering Program'
            ],
            [
                'name' => 'Bachelor of Science in Mathematics',
                'short_name' => 'BSMATH',
                'department_id' => 3,
                'description' => 'BS Mathematics Program'
            ]
        ];
        
        $this->db->table('programs')->insertBatch($programs);
        
        // Seed Publications
        $publications = [
            [
                'title' => 'Development of a Research Archive System',
                'authors' => 'John Doe, Jane Smith',
                'abstract' => 'This research focuses on developing a web-based research archive system for EVSU.',
                'keywords' => 'research archive, web development, database',
                'college_id' => 1,
                'department_id' => 1,
                'program_id' => 1,
                'year' => 2024,
                'publication_date' => '2024-03-20',
                'type' => 'thesis',
                'file_path' => 'uploads/publications/sample1.pdf',
                'thumbnail_path' => 'uploads/thumbnails/sample1.jpg'
            ],
            [
                'title' => 'Analysis of Power Systems',
                'authors' => 'Alice Johnson, Bob Wilson',
                'abstract' => 'A comprehensive analysis of power systems in the Philippines.',
                'keywords' => 'power systems, electrical engineering, analysis',
                'college_id' => 1,
                'department_id' => 2,
                'program_id' => 2,
                'year' => 2024,
                'publication_date' => '2024-03-19',
                'type' => 'thesis',
                'file_path' => 'uploads/publications/sample2.pdf',
                'thumbnail_path' => 'uploads/thumbnails/sample2.jpg'
            ],
            [
                'title' => 'Mathematical Modeling in Research',
                'authors' => 'Carol Brown, David Lee',
                'abstract' => 'Application of mathematical models in research methodology.',
                'keywords' => 'mathematics, modeling, research methodology',
                'college_id' => 2,
                'department_id' => 3,
                'program_id' => 3,
                'year' => 2024,
                'publication_date' => '2024-03-18',
                'type' => 'thesis',
                'file_path' => 'uploads/publications/sample3.pdf',
                'thumbnail_path' => 'uploads/thumbnails/sample3.jpg'
            ]
        ];
        
        $this->db->table('publications')->insertBatch($publications);
    }
} 