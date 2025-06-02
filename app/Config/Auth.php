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
        'Student' => 'Student',
        'Faculty & Researcher' => 'Faculty & Researcher',
        'Thesis Adviser' => 'Thesis Adviser',
        'University Administration' => 'University Administration',
    ];

    /**
     * Default role for new users
     */
    public string $defaultRole = 'Student';

    /**
     * Email verification required
     */
    public bool $requireEmailVerification = false;

    /**
     * Email verification expiry in hours
     */
    public int $emailVerificationExpiry = 24;
} 