<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'message',
        'type',
        'enrollment_id',
        'is_read',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';  // No updated_at field

    protected $skipValidation = false;

    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    public function getNotificationsForUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->findAll();
    }

    /**
     * Get all notifications for a user (optionally paginated)
     *
     * @param int $userId
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function getAllNotificationsForUser($userId, $limit = null, $offset = 0)
    {
        $builder = $this->where('user_id', $userId)
                        ->orderBy('created_at', 'DESC');

        if ($limit !== null) {
            return $builder->findAll($limit, $offset);
        }

        return $builder->findAll();
    }

    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, ['is_read' => 1]);
    }
}
