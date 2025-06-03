<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class UserModel extends Model
{
    protected $table = 'actors';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'name', 'email', 'password', 'role', 'is_active',
        'email_verified_at', 'verification_token', 'last_login',
        'login_attempts', 'locked_until', 'profile_picture', 'research_interests'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email|is_unique[actors.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'role' => 'required|in_list[Student,Faculty & Researcher,Thesis Adviser,University Administration]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Name is required',
            'min_length' => 'Name must be at least 3 characters long',
            'max_length' => 'Name cannot exceed 100 characters'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'This email is already registered'
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 8 characters long'
        ],
        'role' => [
            'required' => 'Role is required',
            'in_list' => 'Invalid role selected'
        ]
    ];

    protected $beforeInsert = ['hashPassword', 'setVerificationToken'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password before saving
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Set verification token for new users
     */
    protected function setVerificationToken(array $data)
    {
        if (!isset($data['data']['verification_token'])) {
            $data['data']['verification_token'] = bin2hex(random_bytes(32));
            $data['data']['email_verified_at'] = null;
        }
        return $data;
    }

    /**
     * Check if user is locked out
     */
    public function isLockedOut(int $userId): bool
    {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }

        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            return true;
        }

        return false;
    }

    /**
     * Increment login attempts and lock account if necessary
     */
    public function incrementLoginAttempts(int $userId): void
    {
        $user = $this->find($userId);
        if (!$user) {
            return;
        }

        $attempts = $user['login_attempts'] + 1;
        $data = ['login_attempts' => $attempts];

        // Lock account if max attempts reached
        if ($attempts >= config('Auth')->maxLoginAttempts) {
            $data['locked_until'] = Time::now()->addMinutes(config('Auth')->lockoutDuration)->toDateTimeString();
        }

        $this->update($userId, $data);
    }

    /**
     * Reset login attempts
     */
    public function resetLoginAttempts(int $userId): void
    {
        $this->update($userId, [
            'login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    /**
     * Verify user email
     */
    public function verifyEmail(int $userId, string $token): bool
    {
        $user = $this->find($userId);
        if (!$user || $user['verification_token'] !== $token) {
            return false;
        }

        $this->update($userId, [
            'email_verified_at' => Time::now()->toDateTimeString(),
            'verification_token' => null
        ]);

        return true;
    }

    /**
     * Custom validation rule for password strength
     */
    public function password_strength(string $str): bool
    {
        $requirements = config('Auth')->passwordRequirements;
        
        if ($requirements['uppercase'] && !preg_match('/[A-Z]/', $str)) {
            return false;
        }
        if ($requirements['lowercase'] && !preg_match('/[a-z]/', $str)) {
            return false;
        }
        if ($requirements['numbers'] && !preg_match('/[0-9]/', $str)) {
            return false;
        }
        if ($requirements['special'] && !preg_match('/[^A-Za-z0-9]/', $str)) {
            return false;
        }

        return true;
    }
} 