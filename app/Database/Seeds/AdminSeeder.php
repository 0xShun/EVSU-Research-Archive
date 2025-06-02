<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $userModel = new UserModel();

        // Check if an admin user already exists
        $adminUser = $userModel->where('role', 'University Administration')->first();

        if (empty($adminUser)) {
            // Create a default admin user
            $data = [
                'name'     => 'Admin User',
                'email'    => 'admin@example.com',
                'password' => password_hash('password', PASSWORD_DEFAULT), // ** Change 'password' to a strong password **
                'role'     => 'University Administration',
                'is_active' => true,
            ];

            $userModel->insert($data);

            $this->call('DatabaseSeeder'); // Optional: Call other seeders if needed
        }
    }
}
