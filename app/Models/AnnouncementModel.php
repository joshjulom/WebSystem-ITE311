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
        'created_at'
    ];

    // Dates
    protected $useTimestamps = false; // We're manually handling created_at
    protected $dateFormat = 'datetime';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'content' => 'required|min_length[10]'
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
}
