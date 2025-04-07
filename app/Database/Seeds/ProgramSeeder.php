<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run()
    {
        // Get department IDs
        $departments = $this->db->table('departments')->get()->getResultArray();
        $deptIds = [];
        foreach ($departments as $dept) {
            $deptIds[$dept['short_name']] = $dept['id'];
        }

        $data = [
            // CAAD Programs
            [
                'name' => 'Bachelor of Science in Architecture',
                'short_name' => 'BSAr',
                'department_id' => $deptIds['ARCH'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Interior Design',
                'short_name' => 'BSID',
                'department_id' => $deptIds['ID'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // CAS Programs
            [
                'name' => 'Bachelor of Science in Economics',
                'short_name' => 'BSEcon',
                'department_id' => $deptIds['ECON'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Batsilyer ng Sining sa Filipino',
                'short_name' => 'BSF',
                'department_id' => $deptIds['FIL'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Arts in English Language',
                'short_name' => 'BAEL',
                'department_id' => $deptIds['ENG'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Mathematics',
                'short_name' => 'BSMath',
                'department_id' => $deptIds['MATH'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Environmental Science',
                'short_name' => 'BSES',
                'department_id' => $deptIds['NATSCI'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Chemistry',
                'short_name' => 'BSChem',
                'department_id' => $deptIds['NATSCI'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Statistics',
                'short_name' => 'BSStat',
                'department_id' => $deptIds['MATH'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor in Human Services',
                'short_name' => 'BHumServ',
                'department_id' => $deptIds['HUMSERV'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // CBE Programs
            [
                'name' => 'Bachelor of Science in Entrepreneurship',
                'short_name' => 'BSE',
                'department_id' => $deptIds['BA'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Office Administration',
                'short_name' => 'BSOA',
                'department_id' => $deptIds['BA'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Accountancy',
                'short_name' => 'BSA',
                'department_id' => $deptIds['ACCT'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Marketing',
                'short_name' => 'BSM',
                'department_id' => $deptIds['BA'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // COE Programs
            [
                'name' => 'Bachelor of Secondary Education - Mathematics',
                'short_name' => 'BSEd-Math',
                'department_id' => $deptIds['SECED'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Secondary Education - Science',
                'short_name' => 'BSEd-Sci',
                'department_id' => $deptIds['SECED'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Culture & Arts Education',
                'short_name' => 'BCAEd',
                'department_id' => $deptIds['SECED'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Physical Education',
                'short_name' => 'BPEd',
                'department_id' => $deptIds['SECED'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor in Elementary Education',
                'short_name' => 'BEED',
                'department_id' => $deptIds['ELED'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Technical-Vocational Teacher Education - Food and Service Management',
                'short_name' => 'BTVTEd-FSM',
                'department_id' => $deptIds['TVTED'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Technical-Vocational Teacher Education - Civil and Construction',
                'short_name' => 'BTVTEd-CC',
                'department_id' => $deptIds['TVTED'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Technical-Vocational Teacher Education - Automotive Technology',
                'short_name' => 'BTVTEd-AT',
                'department_id' => $deptIds['TVTED'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Technical-Vocational Teacher Education - Electrical Technology',
                'short_name' => 'BTVTEd-ET',
                'department_id' => $deptIds['TVTED'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Technical-Vocational Teacher Education - Garments, Fashion & Design',
                'short_name' => 'BTVTEd-GFD',
                'department_id' => $deptIds['TVTED'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Technical-Vocational Teacher Education - HVAC and Refrigeration Technology',
                'short_name' => 'BTVTEd-HVAC',
                'department_id' => $deptIds['TVTED'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // CENG Programs
            [
                'name' => 'Bachelor of Science in Chemical Engineering',
                'short_name' => 'BSChE',
                'department_id' => $deptIds['CHE'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Civil Engineering',
                'short_name' => 'BSCE',
                'department_id' => $deptIds['CE'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Electrical Engineering',
                'short_name' => 'BSEE',
                'department_id' => $deptIds['EEE'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Electronics Engineering',
                'short_name' => 'BSECE',
                'department_id' => $deptIds['EEE'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Geodetic Engineering',
                'short_name' => 'BSGE',
                'department_id' => $deptIds['CE'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Mechanical Engineering',
                'short_name' => 'BSME',
                'department_id' => $deptIds['IE'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Industrial Engineering',
                'short_name' => 'BSIE',
                'department_id' => $deptIds['IE'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Information Technology',
                'short_name' => 'BSIT',
                'department_id' => $deptIds['IT'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // COT Programs
            [
                'name' => 'Bachelor of Science in Hospitality Management',
                'short_name' => 'BSHM',
                'department_id' => $deptIds['HM'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Science in Nutrition & Dietetics',
                'short_name' => 'BSND',
                'department_id' => $deptIds['HM'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bachelor of Industrial Technology',
                'short_name' => 'BIndTech',
                'department_id' => $deptIds['INDTECH'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('programs')->insertBatch($data);
    }
} 