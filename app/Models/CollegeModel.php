<?php

namespace App\Models;

use CodeIgniter\Model;

class CollegeModel extends Model
{
    protected $table = 'colleges';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'short_name', 'description'];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'short_name' => 'required|min_length[2]|max_length[10]',
        'description' => 'permit_empty|max_length[1000]'
    ];
}