<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProgramModel;

class Programs extends ResourceController
{
    protected $programModel;

    public function __construct()
    {
        $this->programModel = new ProgramModel();
    }

    public function getByDepartment($departmentId = null)
    {
        if ($departmentId === null) {
            return $this->fail('Department ID is required');
        }

        $programs = $this->programModel->where('department_id', $departmentId)->findAll();
        return $this->respond($programs);
    }
} 