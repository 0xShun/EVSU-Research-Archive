<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class UserController extends Controller
{
    public function register()
    {
        $userModel = new UserModel();

        if ($this->request->getMethod() === 'post') {
            log_message('info', 'Register: POST request received.');
            $rules = [
                'username' => 'required|min_length[3]|max_length[255]',
                'email' => 'required|valid_email|max_length[255]',
                'password' => 'required|min_length[8]'
            ];

            if (!$this->validate($rules)) {
                // Log validation errors
                log_message('error', 'Register: Validation failed: ' . print_r($this->validator->getErrors(), true));
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            log_message('info', 'Register: Validation passed.');
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role' => 'user' // Default role
            ];

            if ($userModel->save($data)) {
                log_message('info', 'Register: User saved successfully.');
                log_message('info', 'Register: Redirecting to user/login.');
                return redirect()->to('user/login')->with('message', 'Registration successful. Please log in.');
            } else {
                log_message('error', 'Register: Failed to save user.');
                return redirect()->back()->withInput()->with('errors', ['Failed to register user. Please try again.']);
            }
        }

        // Load the register view for GET requests
        return view('register');
    }

    public function login()
    {
        $userModel = new UserModel();

        if ($this->request->getMethod() === 'post') {
            log_message('info', 'Login: POST request received.');
            $rules = [
                'username' => 'required',
                'password' => 'required'
            ];

            if (!$this->validate($rules)) {
                // Log validation errors
                log_message('error', 'Login: Validation failed: ' . print_r($this->validator->getErrors(), true));
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            log_message('info', 'Login: Validation passed.');
            $user = $userModel->getUserByUsername($this->request->getPost('username'));

            if (!$user || !password_verify($this->request->getPost('password'), $user['password'])) {
                // Log login failure
                log_message('error', 'Login: Invalid username or password.');
                return redirect()->back()->withInput()->with('errors', ['Invalid username or password.']);
            }

            log_message('info', 'Login: Authentication successful.');
            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'profile_picture' => $user['profile_picture'],
                'isLoggedIn' => true
            ]);

            session()->setFlashdata('message', 'Successfully logged in.');
            log_message('info', 'Login: Redirecting to home page.');
            return redirect()->to('/');
        } else {
            // Load the login view for GET requests
            return view('login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('message', 'Logged out successfully.');
    }

    public function profile()
    {
        // User profile logic
    }
} 