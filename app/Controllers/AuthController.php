<?php

namespace App\Controllers;

use App\Libraries\Auth;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class AuthController extends BaseController
{
    protected $auth;
    protected $userModel;
    protected $email;

    public function __construct()
    {
        $this->auth = new Auth();
        $this->userModel = new UserModel();
        $this->email = \Config\Services::email();
    }

    /**
     * Show login form
     */
    public function login()
    {
        // If already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            return redirect()->to(base_url());
        }

        return view('auth/login');
    }

    /**
     * Handle login attempt
     */
    public function attemptLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        // Check if user exists
        $user = $this->userModel->where('email', $email)->first();
        if (!$user) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Invalid email or password.');
        }

        // Check if account is locked
        if ($this->userModel->isLockedOut($user['id'])) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Account is locked. Please try again later.');
        }

        // Attempt login
        if (!$this->auth->attempt($email, $password, $remember)) {
            // Increment login attempts
            $this->userModel->incrementLoginAttempts($user['id']);
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Invalid email or password.');
        }

        // Check if email is verified
        if (config('Auth')->requireEmailVerification && !$user['email_verified_at']) {
            $this->auth->logout();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Please verify your email address first.');
        }

        // Reset login attempts on successful login
        $this->userModel->resetLoginAttempts($user['id']);

        // Redirect to intended page or home
        $redirect = session()->get('redirect_after_login') ?? base_url();
        session()->remove('redirect_after_login');

        return redirect()->to($redirect)
                        ->with('success', 'Welcome back, ' . $user['name'] . '!');
    }

    /**
     * Show registration form
     */
    public function register()
    {
        // If already logged in, redirect to home
        if ($this->auth->isLoggedIn()) {
            return redirect()->to(base_url());
        }

        return view('auth/register');
    }

    /**
     * Handle registration
     */
    public function attemptRegister()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Manual password strength check
        $password = $this->request->getPost('password');
        if (!$this->userModel->password_strength($password)) {
             $this->validator->setError('password', 'Password must contain uppercase, lowercase, numbers, and special characters.');
             return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // Create user
        $userData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $password,
            'role' => config('Auth')->defaultRole,
            'is_active' => true
        ];

        $userId = $this->userModel->insert($userData);
        if (!$userId) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Registration failed. Please try again.');
        }

        // Send verification email
        if (config('Auth')->requireEmailVerification) {
            $user = $this->userModel->find($userId);
            $this->sendVerificationEmail($user);
        }

        return redirect()->to(base_url('auth/login'))
                        ->with('success', 'Registration successful! ' . 
                              (config('Auth')->requireEmailVerification ? 
                               'Please check your email to verify your account.' : 
                               'You can now login.'));
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        $this->auth->logout();
        return redirect()->to(base_url('login'))
                        ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show forgot password form
     */
    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    /**
     * Handle forgot password request
     */
    public function attemptForgotPassword()
    {
        $rules = ['email' => 'required|valid_email'];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $token = $this->auth->generatePasswordResetToken($email);

        if ($token) {
            $this->sendPasswordResetEmail($email, $token);
        }

        // Always show success message to prevent email enumeration
        return redirect()->to(base_url('login'))
                        ->with('success', 'If your email is registered, you will receive password reset instructions.');
    }

    /**
     * Show reset password form
     */
    public function resetPassword($token)
    {
        $email = $this->request->getGet('email');
        if (!$email || !$this->auth->verifyPasswordResetToken($email, $token)) {
            return redirect()->to(base_url('login'))
                           ->with('error', 'Invalid or expired password reset link.');
        }

        return view('auth/reset_password', ['email' => $email, 'token' => $token]);
    }

    /**
     * Handle password reset
     */
    public function attemptResetPassword()
    {
        $rules = [
            'email' => 'required|valid_email',
            'token' => 'required',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        if (!$this->auth->verifyPasswordResetToken($email, $token)) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Invalid or expired password reset link.');
        }

        // Manual password strength check
        if (!$this->userModel->password_strength($password)) {
             $this->validator->setError('password', 'Password must contain uppercase, lowercase, numbers, and special characters.');
             return redirect()->back()
                            ->withInput()
                            ->with('errors', $this->validator->getErrors());
        }

        // Update password
        $user = $this->userModel->where('email', $email)->first();
        $this->userModel->update($user['id'], ['password' => $password]);
        $this->auth->clearPasswordResetToken($email);

        return redirect()->to(base_url('auth/login'))
                        ->with('success', 'Password has been reset successfully. You can now login.');
    }

    /**
     * Verify email address
     */
    public function verifyEmail($token)
    {
        $user = $this->userModel->where('verification_token', $token)->first();
        
        if (!$user) {
            return redirect()->to(base_url('login'))
                           ->with('error', 'Invalid verification link.');
        }

        if ($this->userModel->verifyEmail($user['id'], $token)) {
            return redirect()->to(base_url('login'))
                           ->with('success', 'Email verified successfully. You can now login.');
        }

        return redirect()->to(base_url('login'))
                        ->with('error', 'Email verification failed.');
    }

    /**
     * Resend verification email
     */
    public function resendVerification()
    {
        if (!$this->auth->isLoggedIn()) {
            return redirect()->to(base_url('login'));
        }

        $user = $this->auth->user();
        if ($user['email_verified_at']) {
            return redirect()->to(base_url())
                           ->with('error', 'Email is already verified.');
        }

        $this->sendVerificationEmail($user);

        return redirect()->back()
                        ->with('success', 'Verification email has been sent. Please check your inbox.');
    }

    /**
     * Send verification email
     */
    protected function sendVerificationEmail(array $user): void
    {
        $this->email->setFrom('noreply@evsu.edu.ph', 'EVSU Research Archive');
        $this->email->setTo($user['email']);
        $this->email->setSubject('Verify Your Email Address');
        
        $data = [
            'name' => $user['name'],
            'verification_link' => base_url("verify-email/{$user['verification_token']}")
        ];
        
        $this->email->setMessage(view('emails/verify_email', $data));
        $this->email->send();
    }

    /**
     * Send password reset email
     */
    protected function sendPasswordResetEmail(string $email, string $token): void
    {
        $this->email->setFrom('noreply@evsu.edu.ph', 'EVSU Research Archive');
        $this->email->setTo($email);
        $this->email->setSubject('Reset Your Password');
        
        $data = [
            'reset_link' => base_url("reset-password/{$token}?email=" . urlencode($email))
        ];
        
        $this->email->setMessage(view('emails/reset_password', $data));
        $this->email->send();
    }
} 