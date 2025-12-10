<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Teacher extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function dashboard()
    {
        $session = session();

        // Authorization: ensure user is logged in and is a teacher
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access the dashboard.');
            return redirect()->to('login');
        }

        if ($session->get('role') !== 'teacher') {
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/announcements');
        }

        // Check if user account is active
        $user = $this->userModel->find($session->get('user_id'));
        if (!$user || ($user['status'] ?? 'active') !== 'active') {
            $session->destroy();
            $session->setFlashdata('login_error', 'Your account has been deactivated.');
            return redirect()->to('login');
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $session->get('role')
        ];

        return view('teacher_dashboard', $data);
    }
}
