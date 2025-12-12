<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Announcement extends Controller
{
    protected $announcementModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
    }

    /**
     * Public view of all announcements
     */
    public function index()
    {
        $role = session()->get('role') ?? 'all';
        $announcements = $this->announcementModel->getActiveAnnouncementsFor($role, 20);
        
        $data = [
            'announcements' => $announcements,
            'role' => $role
        ];
        
        return view('announcements', $data);
    }

    /**
     * Admin/Teacher: Manage all announcements
     */
    public function manage()
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Teachers can only see their own announcements, admins see all
        if (session()->get('role') === 'teacher') {
            $announcements = $this->announcementModel->where('created_by', session()->get('user_id'))
                                                     ->orderBy('created_at', 'DESC')
                                                     ->findAll();
            // Add creator name manually for teachers viewing their own
            foreach ($announcements as &$announcement) {
                $announcement['creator_name'] = session()->get('user_name');
            }
        } else {
            $announcements = $this->announcementModel->getAllWithCreator();
        }

        return view('announcements/manage', [
            'announcements' => $announcements
        ]);
    }

    /**
     * Admin/Teacher: Create announcement form
     */
    public function create()
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        return view('announcements/create');
    }

    /**
     * Admin/Teacher: Store new announcement
     */
    public function store()
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required|min_length[10]',
            'target_audience' => 'required|in_list[all,admin,teacher,student]',
            'priority' => 'required|in_list[low,normal,high,urgent]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'target_audience' => $this->request->getPost('target_audience'),
            'priority' => $this->request->getPost('priority'),
            'expires_at' => $this->request->getPost('expires_at') ?: null,
            'is_active' => 1,
            'created_by' => session()->get('user_id')
        ];

        if ($this->announcementModel->insert($data)) {
            // Send notifications to target audience
            $this->sendNotificationsToAudience($data['target_audience'], $data['title']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement created successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create announcement'
            ]);
        }
    }

    /**
     * Admin/Teacher: Edit announcement
     */
    public function edit($id)
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $announcement = $this->announcementModel->find($id);
        
        if (!$announcement) {
            return redirect()->to('/announcement/manage')->with('error', 'Announcement not found');
        }

        // Teachers can only edit their own announcements
        if (session()->get('role') === 'teacher' && $announcement['created_by'] != session()->get('user_id')) {
            return redirect()->to('/announcement/manage')->with('error', 'You can only edit your own announcements');
        }

        return view('announcements/edit', [
            'announcement' => $announcement
        ]);
    }

    /**
     * Admin/Teacher: Update announcement
     */
    public function update($id)
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Teachers can only update their own announcements
        $announcement = $this->announcementModel->find($id);
        if (session()->get('role') === 'teacher' && $announcement['created_by'] != session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'You can only update your own announcements']);
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required|min_length[10]',
            'target_audience' => 'required|in_list[all,admin,teacher,student]',
            'priority' => 'required|in_list[low,normal,high,urgent]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'target_audience' => $this->request->getPost('target_audience'),
            'priority' => $this->request->getPost('priority'),
            'expires_at' => $this->request->getPost('expires_at') ?: null
        ];

        if ($this->announcementModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update announcement'
            ]);
        }
    }

    /**
     * Admin/Teacher: Toggle announcement status
     */
    public function toggleStatus($id)
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Teachers can only toggle their own announcements
        $announcement = $this->announcementModel->find($id);
        if (session()->get('role') === 'teacher' && $announcement['created_by'] != session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'You can only toggle your own announcements']);
        }

        if ($this->announcementModel->toggleActive($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update status'
            ]);
        }
    }

    /**
     * Admin/Teacher: Delete announcement
     */
    public function delete($id)
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Teachers can only delete their own announcements
        $announcement = $this->announcementModel->find($id);
        if (session()->get('role') === 'teacher' && $announcement['created_by'] != session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'You can only delete your own announcements']);
        }

        if ($this->announcementModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete announcement'
            ]);
        }
    }

    /**
     * Send notifications to target audience
     */
    private function sendNotificationsToAudience($audience, $title)
    {
        $userModel = new \App\Models\UserModel();
        $notificationModel = new NotificationModel();

        $users = [];
        
        if ($audience === 'all') {
            $users = $userModel->findAll();
        } else {
            $users = $userModel->where('role', $audience)->findAll();
        }

        foreach ($users as $user) {
            $notificationModel->insert([
                'user_id' => $user['id'],
                'message' => 'New announcement: ' . $title,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
