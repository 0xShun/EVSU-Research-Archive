<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAccessFilter implements FilterInterface
{
    /**
     * Allowed admin routes for thesis advisers and faculty/researchers
     */
    protected $allowedRoutes = [
        'admin',                    // Dashboard
        'admin/view-analytics',     // View Analytics
        'admin/manage-submissions', // Manage Submissions
        'admin/submissions/approve', // Approve submissions
        'admin/submissions/reject'   // Reject submissions
    ];

    /**
     * Check if the current route is allowed for the user's role
     */
    protected function isRouteAllowed(string $uri): bool
    {
        // Remove 'index.php/' from the beginning of the URI if it exists
        $cleanedUri = str_replace('index.php/', '', $uri);

        // Check for exact match in allowed routes, including the empty string for the base admin URL
        return in_array($cleanedUri, $this->allowedRoutes) || ($cleanedUri === '' && in_array('admin', $this->allowedRoutes));
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userRole = $session->get('role');
        $currentUri = trim($request->getURI()->getPath(), '/');

        // Add logging here to inspect values
        log_message('debug', 'AdminAccessFilter: User Role: ' . $userRole);
        log_message('debug', 'AdminAccessFilter: Current URI: ' . $currentUri);

        // If the URI is empty (root), treat it as 'admin' for filter checks
        if ($currentUri === '') {
           $currentUri = 'admin';
        }

        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
             return redirect()->to(base_url('auth/login'))->with('error', 'Please login to access the admin area.');
        }

        // Allow full admin access to University Administration immediately
        if ($userRole === 'University Administration') {
            return; // Allow access for University Administration
        }

        // Redirect Student role to the homepage
        if ($userRole === 'Student') {
            session()->setFlashdata('error', 'You do not have permission to access the admin area.');
            return redirect()->to(base_url('/'));
        }

        // For Thesis Adviser and Faculty & Researcher roles, check if the route is allowed
        if (in_array($userRole, ['Thesis Adviser', 'Faculty & Researcher'])) {
            // Add logging here to see the value before the check
            log_message('debug', 'AdminAccessFilter [Limited Role Check]: User Role: ' . $userRole);
            log_message('debug', 'AdminAccessFilter [Limited Role Check]: Current URI before check: ' . $currentUri);
            log_message('debug', 'AdminAccessFilter [Limited Role Check]: isRouteAllowed(' . $currentUri . '): ' . ($this->isRouteAllowed($currentUri) ? 'true' : 'false'));

            // Check if the current URI is NOT allowed for this role
            if (!$this->isRouteAllowed($currentUri)) {
                // Set a flash message
                session()->setFlashdata('error', 'You do not have permission to access this page.');
                // Redirect to the admin dashboard
                return redirect()->to(base_url('admin'));
            }
        } else {
            // If the user is logged in but does not have one of the recognized admin roles,
            // deny access and redirect to the dashboard.
            session()->setFlashdata('error', 'You do not have sufficient permissions to access this area.');
            return redirect()->to(base_url('admin'));
        }

        // If the user has a valid limited role and the route is allowed, continue
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
} 