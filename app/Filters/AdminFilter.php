<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Log session data before access check
        log_message('debug', 'AdminFilter: Session data - ' . print_r(session()->get(), true));

        // Ensure the authentication service is available
        $auth = service('auth');

        // Check if the auth service was successfully retrieved and if the user is logged in with the correct role
        if ($auth === null || !$auth->isLoggedIn() || !$auth->hasRole('University Administration')) {
            // Log the reason for denial (optional, for debugging)
            if ($auth === null) { log_message('error', 'AdminFilter: Auth service is null.'); }
            else if (!$auth->isLoggedIn()) { log_message('debug', 'AdminFilter: User not logged in.'); }
            else { log_message('debug', 'AdminFilter: User role ' . session()->get('role') . ' is not University Administration.'); }

            return redirect()->to(base_url('auth/login'))->with('error', 'You do not have permission to access this page.');
        }

        // If checks pass, the user is a University Administrator and can proceed
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
