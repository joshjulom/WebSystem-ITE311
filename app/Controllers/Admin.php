<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Admin extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function dashboard()
    {
        $session = session();

        // Authorization: ensure user is logged in and is an admin
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access the dashboard.');
            return redirect()->to('login');
        }

        if ($session->get('role') !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/dashboard');
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

        return view('admin_dashboard', $data);
    }

    public function users()
    {
        $session = session();

        // Authorization: ensure user is logged in and is an admin
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to access the dashboard.');
            return redirect()->to('login');
        }

        if ($session->get('role') !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/dashboard');
        }

        // Check if user account is active
        $user = $this->userModel->find($session->get('user_id'));
        if (!$user || ($user['status'] ?? 'active') !== 'active') {
            $session->destroy();
            $session->setFlashdata('login_error', 'Your account has been deactivated.');
            return redirect()->to('login');
        }

        $users = $this->userModel->findAll(); // Show all users including inactive

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $session->get('role'),
            'users' => $users
        ];

        return view('admin_users', $data);
    }

    public function updateRole()
    {
        $session = session();

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $this->request->getPost('user_id');
        $newRole = $this->request->getPost('role');

        if (!$userId || !$newRole) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        // Prevent changing the main admin's role (assuming ID 1 or email admin@example.com)
        $user = $this->userModel->find($userId);
        if (!$user || ($user['id'] == 1 || $user['email'] == 'admin@example.com')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot change role of main admin']);
        }

        $this->userModel->update($userId, ['role' => $newRole]);

        return $this->response->setJSON(['success' => true, 'message' => 'Role updated successfully']);
    }

    public function addUser()
    {
        $session = session();

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $role = $this->request->getPost('role');

        // Validate inputs
        if (!$name || !$email || !$role) {
            return $this->response->setJSON(['success' => false, 'message' => 'All fields are required']);
        }

        // Validate name format (server-side validation)
        if (!preg_match("/^[A-Za-z\s\-']+$/", $name)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Name can only contain letters, spaces, hyphens, and apostrophes']);
        }

        // Check for duplicate email
        if ($this->userModel->where('email', $email)->first()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email already exists']);
        }

        // Use default password
        $defaultPassword = 'password123';

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $defaultPassword,
            'role' => $role
        ];

        if ($this->userModel->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User added successfully with default password: password123']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to add user']);
        }
    }

    public function toggleStatus($id)
    {
        $session = session();

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        // Prevent deactivating the main admin
        if ($id == 1) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot deactivate main admin']);
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $action = $newStatus === 'active' ? 'activated' : 'deactivated';

        if ($this->userModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON(['success' => true, 'message' => "User $action successfully", 'new_status' => $newStatus]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => "Failed to $action user"]);
        }
    }

    public function updateUser($id)
    {
        $session = session();

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        // Prevent editing the main admin except password
        if ($id == 1) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot edit main admin']);
        }

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $role = $this->request->getPost('role');

        $data = [];
        if ($name) $data['name'] = $name;
        if ($email) $data['email'] = $email;
        if ($role) $data['role'] = $role;

        if ($this->userModel->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user']);
        }
    }

    public function changePassword($id)
    {
        $session = session();

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $password = $this->request->getPost('password');

        if (!$password || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid password']);
        }

        if ($this->userModel->update($id, ['password' => $password])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Password changed successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to change password']);
        }
    }
}
