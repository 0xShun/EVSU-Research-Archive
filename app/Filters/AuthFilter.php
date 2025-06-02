<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\Auth;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = new Auth();
        
        if (!$auth->isLoggedIn()) {
            // Store the intended URL in session
            $session = \Config\Services::session();
            $session->set('redirect_after_login', current_url());
            
            // Redirect to login page
            return redirect()->to(base_url('login'))
                           ->with('error', 'Please login to access this page.');
        }

        // Check role if specified
        if (!empty($arguments)) {
            $role = $arguments[0];
            if (!$auth->hasRole($role)) {
                return redirect()->to(base_url())
                               ->with('error', 'You do not have permission to access this page.');
            }
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
} 