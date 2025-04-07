<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Department extends Seeder
{
    public function run()
    {
        // Get college IDs
        $colleges = $this->db->table('colleges')->get()->getResultArray();
        $collegeIds = [];
        foreach ($colleges as $college) {
            $collegeIds[$college['short_name']] = $college['id'];
        }

        $data = [
            // CAAD Departments
            [
                'name' => 'Department of Architecture',
                'short_name' => 'ARCH',
                'college_id' => $collegeIds['CAAD']
            ],
            [
                'name' => 'Department of Interior Design',
                'short_name' => 'ID',
                'college_id' => $collegeIds['CAAD']
            ],

            // CAS Departments
            [
                'name' => 'Department of Economics',
                'short_name' => 'ECON',
                'college_id' => $collegeIds['CAS']
            ],
            [
                'name' => 'Department of Filipino',
                'short_name' => 'FIL',
                'college_id' => $collegeIds['CAS']
            ],
            [
                'name' => 'Department of English',
                'short_name' => 'ENG',
                'college_id' => $collegeIds['CAS']
            ],
            [
                'name' => 'Department of Mathematics',
                'short_name' => 'MATH',
                'college_id' => $collegeIds['CAS']
            ],
            [
                'name' => 'Department of Natural Sciences',
                'short_name' => 'NATSCI',
                'college_id' => $collegeIds['CAS']
            ],
            [
                'name' => 'Department of Human Services',
                'short_name' => 'HUMSERV',
                'college_id' => $collegeIds['CAS']
            ],

            // CBE Departments
            [
                'name' => 'Department of Business Administration',
                'short_name' => 'BA',
                'college_id' => $collegeIds['CBE']
            ],
            [
                'name' => 'Department of Accountancy',
                'short_name' => 'ACCT',
                'college_id' => $collegeIds['CBE']
            ],

            // COE Departments
            [
                'name' => 'Department of Secondary Education',
                'short_name' => 'SECED',
                'college_id' => $collegeIds['COE']
            ],
            [
                'name' => 'Department of Elementary Education',
                'short_name' => 'ELED',
                'college_id' => $collegeIds['COE']
            ],
            [
                'name' => 'Department of Technical-Vocational Teacher Education',
                'short_name' => 'TVTED',
                'college_id' => $collegeIds['COE']
            ],

            // CENG Departments
            [
                'name' => 'Department of Chemical Engineering',
                'short_name' => 'CHE',
                'college_id' => $collegeIds['CENG']
            ],
            [
                'name' => 'Department of Civil Engineering',
                'short_name' => 'CE',
                'college_id' => $collegeIds['CENG']
            ],
            [
                'name' => 'Department of Electrical and Electronics Engineering',
                'short_name' => 'EEE',
                'college_id' => $collegeIds['CENG']
            ],
            [
                'name' => 'Department of Industrial Engineering',
                'short_name' => 'IE',
                'college_id' => $collegeIds['CENG']
            ],
            [
                'name' => 'Department of Information Technology',
                'short_name' => 'IT',
                'college_id' => $collegeIds['CENG']
            ],

            // COT Departments
            [
                'name' => 'Department of Hospitality Management',
                'short_name' => 'HM',
                'college_id' => $collegeIds['COT']
            ],
            [
                'name' => 'Department of Industrial Technology',
                'short_name' => 'INDTECH',
                'college_id' => $collegeIds['COT']
            ]
        ];

        $this->db->table('departments')->insertBatch($data);
    }
}
