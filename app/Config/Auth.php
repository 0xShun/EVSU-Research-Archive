<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig
{
    /**
     * Session timeout in seconds (30 minutes)
     */
    public int $sessionTimeout = 1800;

    /**
     * Remember me token expiry in days
     */
    public int $rememberMeExpiry = 30;

    /**
     * Password reset token expiry in hours
     */
    public int $passwordResetExpiry = 1;

    /**
     * Maximum login attempts before lockout
     */
    public int $maxLoginAttempts = 5;

    /**
     * Lockout duration in minutes
     */
    public int $lockoutDuration = 15;

    /**
     * Minimum password length
     */
    public int $minPasswordLength = 8;

    /**
     * Password requirements
     */
    public array $passwordRequirements = [
        'uppercase' => true,  // Require uppercase letters
        'lowercase' => true,  // Require lowercase letters
        'numbers' => true,    // Require numbers
        'special' => true,    // Require special characters
    ];

    /**
     * Allowed roles
     */
    public array $roles = [
        'admin' => 'Administrator',
        'faculty' => 'Faculty Member',
        'student' => 'Student',
        'researcher' => 'Researcher'
    ];

    /**
     * Default role for new users
     */
    public string $defaultRole = 'student';

    /**
     * Email verification required
     */
    public bool $requireEmailVerification = true;

    /**
     * Email verification expiry in hours
     */
    public int $emailVerificationExpiry = 24;
} 