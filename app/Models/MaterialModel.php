<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['course_id', 'file_name', 'file_path', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Insert a new material record.
     *
     * @param array $data
     * @return bool
     */
    public function insertMaterial($data)
    {
        return $this->insert($data);
    }

    /**
     * Get all materials for a specific course.
     *
     * @param int $course_id
     * @return array
     */
    public function getMaterialsByCourse($course_id)
    {
        return $this->where('course_id', $course_id)->findAll();
    }
}
