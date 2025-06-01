<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username', 
        'email', 
        'password', 
        'role',
        'profile_picture',
        'full_name',
        'title',
        'research_interests'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[255]',
        'email' => 'required|valid_email|max_length[255]',
        'password' => 'required|min_length[8]',
        'full_name' => 'required|max_length[255]',
        'title' => 'permit_empty|max_length[255]',
        'research_interests' => 'permit_empty|max_length[1000]',
        'profile_picture' => 'permit_empty|max_length[255]'
    ];

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
} 