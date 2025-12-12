<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentQuestionModel extends Model
{
    protected $table            = 'assignment_questions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'assignment_id',
        'question_type',
        'question_text',
        'options',
        'correct_answer',
        'max_points',
        'order_position'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'assignment_id' => 'required|integer',
        'question_type' => 'required|in_list[multiple_choice,essay,file_upload]',
        'max_points' => 'required|decimal|greater_than_equal_to[0]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
