<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'title',
        'content',
        'target_audience',
        'priority',
        'created_by',
        'created_at',
        'expires_at',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = false; // We're manually handling created_at
    protected $dateFormat = 'datetime';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'content' => 'required|min_length[10]',
        'target_audience' => 'permit_empty|in_list[all,admin,teacher,student]',
        'priority' => 'permit_empty|in_list[low,normal,high,urgent]'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'The announcement title is required.',
            'min_length' => 'The announcement title must be at least 3 characters long.',
            'max_length' => 'The announcement title cannot exceed 255 characters.'
        ],
        'content' => [
            'required' => 'The announcement content is required.',
            'min_length' => 'The announcement content must be at least 10 characters long.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['setCreatedAt'];
    protected $beforeUpdate = [];

    protected function setCreatedAt(array $data)
    {
        if (!isset($data['data']['created_at'])) {
            $data['data']['created_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Get active announcements for a specific audience
     */
    public function getActiveAnnouncementsFor($audience = 'all', $limit = 5)
    {
        $builder = $this->where('is_active', 1);
        
        // Filter by target audience
        if ($audience !== 'all') {
            $builder->groupStart()
                   ->where('target_audience', $audience)
                   ->orWhere('target_audience', 'all')
                   ->groupEnd();
        }
        
        // Filter out expired announcements
        $builder->groupStart()
               ->where('expires_at IS NULL')
               ->orWhere('expires_at >', date('Y-m-d H:i:s'))
               ->groupEnd();
        
        return $builder->orderBy('priority', 'DESC')
                      ->orderBy('created_at', 'DESC')
                      ->limit($limit)
                      ->find();
    }

    /**
     * Get all announcements with creator info (for admin management)
     */
    public function getAllWithCreator()
    {
        return $this->select('announcements.*, users.name as creator_name')
                    ->join('users', 'users.id = announcements.created_by', 'left')
                    ->orderBy('announcements.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Toggle announcement active status
     */
    public function toggleActive($id)
    {
        $announcement = $this->find($id);
        if ($announcement) {
            $newStatus = $announcement['is_active'] == 1 ? 0 : 1;
            return $this->update($id, ['is_active' => $newStatus]);
        }
        return false;
    }
}
