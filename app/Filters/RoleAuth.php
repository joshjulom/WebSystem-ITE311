<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAuth implements FilterInterface
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
        $session = session();
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access this page.');
            return redirect()->to('login');
        }
        
        $userRole = $session->get('role');
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        // Check role-based access
        switch ($userRole) {
            case 'admin':
                // Admins can access any route starting with /admin
                if (strpos($path, '/admin') === 0) {
                    return; // Allow access
                }
                break;
                
            case 'teacher':
                // Teachers can only access routes starting with /teacher
                if (strpos($path, '/teacher') === 0) {
                    return; // Allow access
                }
                break;
                
            case 'student':
                // Students can access routes starting with /student and /announcements
                if (strpos($path, '/student') === 0 || $path === '/announcements') {
                    return; // Allow access
                }
                break;
        }
        
        // If user tries to access a route not permitted for their role
        $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
        return redirect()->to('/dashboard');
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
