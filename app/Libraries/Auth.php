<?php

namespace App\Libraries;

use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class Auth
{
    protected $userModel;
    protected $session;
    protected $db;
    protected $config;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->config = config('Auth');
    }

    /**
     * Attempt to authenticate a user
     */
    public function attempt(string $email, string $password, bool $remember = false): bool
    {
        $user = $this->userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        if (!$user['is_active']) {
            return false;
        }

        // Update last login
        $this->userModel->update($user['id'], [
            'last_login' => Time::now()->toDateTimeString(),
            'login_attempts' => 0
        ]);

        // Set session data
        $this->setSession($user);

        // Handle remember me
        if ($remember) {
            $this->rememberUser($user['id']);
        }

        return true;
    }

    /**
     * Set user session data
     */
    protected function setSession(array $user): void
    {
        $sessionData = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role'],
            'is_logged_in' => true,
            'last_activity' => time()
        ];

        $this->session->set($sessionData);
    }

    /**
     * Handle remember me functionality
     */
    protected function rememberUser(int $userId): void
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));
        $hashedValidator = password_hash($validator, PASSWORD_DEFAULT);
        
        $expires = Time::now()->addDays(30)->getTimestamp();

        // Store in database
        $this->db->table('auth_tokens')->insert([
            'user_id' => $userId,
            'selector' => $selector,
            'hashed_validator' => $hashedValidator,
            'expires' => date('Y-m-d H:i:s', $expires)
        ]);

        // Set cookie
        $cookieValue = $selector . ':' . $validator;
        setcookie(
            'remember_token',
            $cookieValue,
            $expires,
            '/',
            '',
            true, // Secure
            true  // HttpOnly
        );
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn(): bool
    {
        if (!$this->session->get('is_logged_in')) {
            return $this->checkRememberToken();
        }

        // Check session timeout
        if (time() - $this->session->get('last_activity') > $this->config->sessionTimeout) {
            $this->logout();
            return false;
        }

        // Update last activity
        $this->session->set('last_activity', time());
        return true;
    }

    /**
     * Check remember me token
     */
    protected function checkRememberToken(): bool
    {
        if (!isset($_COOKIE['remember_token'])) {
            return false;
        }

        list($selector, $validator) = explode(':', $_COOKIE['remember_token']);

        $token = $this->db->table('auth_tokens')
            ->where('selector', $selector)
            ->where('expires >', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        if (!$token || !password_verify($validator, $token['hashed_validator'])) {
            return false;
        }

        // Get user and set session
        $user = $this->userModel->find($token['user_id']);
        if ($user) {
            $this->setSession($user);
            return true;
        }

        return false;
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        // Remove remember token
        if (isset($_COOKIE['remember_token'])) {
            list($selector, ) = explode(':', $_COOKIE['remember_token']);
            $this->db->table('auth_tokens')->where('selector', $selector)->delete();
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }

        // Destroy session
        $this->session->destroy();
    }

    /**
     * Get current user
     */
    public function user(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return $this->userModel->find($this->session->get('user_id'));
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->session->get('role') === $role;
    }

    /**
     * Generate password reset token
     */
    public function generatePasswordResetToken(string $email): ?string
    {
        $user = $this->userModel->where('email', $email)->first();
        if (!$user) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $expires = Time::now()->addHours(1)->getTimestamp();

        $this->db->table('password_resets')->insert([
            'email' => $email,
            'token' => password_hash($token, PASSWORD_DEFAULT),
            'expires' => date('Y-m-d H:i:s', $expires)
        ]);

        return $token;
    }

    /**
     * Verify password reset token
     */
    public function verifyPasswordResetToken(string $email, string $token): bool
    {
        $reset = $this->db->table('password_resets')
            ->where('email', $email)
            ->where('expires >', date('Y-m-d H:i:s'))
            ->get()
            ->getRowArray();

        if (!$reset) {
            return false;
        }

        return password_verify($token, $reset['token']);
    }

    /**
     * Clear password reset token
     */
    public function clearPasswordResetToken(string $email): void
    {
        $this->db->table('password_resets')->where('email', $email)->delete();
    }
} 