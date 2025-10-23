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

        $userId = session('user_id');
        $notificationModel = new NotificationModel();

        $count = $notificationModel->getUnreadCount($userId);
        $notifications = $notificationModel->getNotificationsForUser($userId);

        return $this->respond([
            'count' => $count,
            'notifications' => $notifications
        ]);
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
