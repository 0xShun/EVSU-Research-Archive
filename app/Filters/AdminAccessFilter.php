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
        'admin',
        'admin/view-analytics',
        'admin/manage-submissions',
        'admin/submissions/approve',
        'admin/submissions/reject'
    ];

    /**
     * Check if the current route is allowed for the user's role
     */
    protected function isRouteAllowed(string $uri): bool
    {
        // Check for exact match in allowed routes
        return in_array($uri, $this->allowedRoutes);
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userRole = $session->get('role');
        $currentUri = trim($request->getURI()->getPath(), '/');

        // If the URI is empty (root), treat it as 'admin' for filter checks
        if ($currentUri === '') {
           $currentUri = 'admin';
        }

        // Check if user is logged in and has any admin-like role
        if (!session()->get('isLoggedIn') || !in_array($userRole, ['University Administration', 'Thesis Adviser', 'Faculty & Researcher'])) {
            return redirect()->to(base_url('auth/login'))->with('error', 'You do not have permission to access the admin area.');
        }

        // Allow full admin access to University Administration
        if ($userRole === 'University Administration') {
            return;
        }

        // For Thesis Adviser and Faculty & Researcher roles, check if the route is allowed
        if (in_array($userRole, ['Thesis Adviser', 'Faculty & Researcher'])) {
            if (!$this->isRouteAllowed($currentUri)) {
                // Show an access denied error if not allowed
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied. You do not have permission to view this page.');
            }
        }

        // If the user has a valid limited role and the route is allowed, continue
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
} 