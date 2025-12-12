<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use CodeIgniter\API\ResponseTrait;

class Notifications extends BaseController
{
    use ResponseTrait;

    public function get()
    {
        if (!session('isLoggedIn')) {
            return $this->respond(['error' => 'Unauthorized'], 401);
        }

        // Check if user account is active
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find(session('user_id'));
        if (!$user || ($user['status'] ?? 'active') !== 'active') {
            return $this->respond(['error' => 'Account deactivated'], 403);
        }

        $userId = session('user_id');
        $notificationModel = new NotificationModel();

        try {
            $count = $notificationModel->getUnreadCount($userId);
            $notifications = $notificationModel->getNotificationsForUser($userId);
            
            // Get enrollment details for enrollment notifications (if columns exist)
            $enrollmentModel = new \App\Models\EnrollmentModel();
            foreach ($notifications as &$notif) {
                // Set default type if not exists (backwards compatibility)
                if (!isset($notif['type'])) {
                    $notif['type'] = 'general';
                }
                if (!isset($notif['enrollment_id'])) {
                    $notif['enrollment_id'] = null;
                }
                
                if ($notif['type'] === 'enrollment' && !empty($notif['enrollment_id'])) {
                    $enrollment = $enrollmentModel->find($notif['enrollment_id']);
                    if ($enrollment) {
                        $notif['enrollment_status'] = $enrollment['status'] ?? 'pending';
                    }
                }
            }

            return $this->respond([
                'count' => $count,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Notification error: ' . $e->getMessage());
            return $this->respond([
                'count' => 0,
                'notifications' => [],
                'error' => 'Failed to load notifications'
            ]);
        }
    }

    public function mark_as_read($id)
    {
        if (!session('isLoggedIn')) {
            return $this->respond(['error' => 'Unauthorized'], 401);
        }

        $notificationModel = new NotificationModel();
        $result = $notificationModel->markAsRead($id);

        if ($result) {
            return $this->respond(['success' => true]);
        } else {
            return $this->respond(['error' => 'Failed to mark as read'], 400);
        }
    }
}
