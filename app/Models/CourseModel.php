<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'course_code',
        'title',
        'description',
        'school_year',
        'semester',
        'start_date',
        'end_date',
        'schedule',
        'instructor_id',
        'status',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'description' => 'required|min_length[10]',
        'instructor_id' => 'required|integer'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get all courses with teacher information
     */
    public function getCoursesWithTeachers()
    {
        return $this->select('courses.*, users.name as teacher_name')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->findAll();
    }

    /**
     * Get all courses with teacher information and enrollment counts
     */
    public function getCoursesWithTeachersAndEnrollments()
    {
        return $this->select('courses.*, users.name as teacher_name, COUNT(DISTINCT enrollments.user_id) as active_users')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->join('enrollments', 'enrollments.course_id = courses.id', 'left')
                    ->groupBy('courses.id')
                    ->findAll();
    }

    /**
     * Get active courses count
     */
    public function getActiveCoursesCount()
    {
        $db = \Config\Database::connect();
        if ($db->fieldExists('status', 'courses')) {
            return $this->where('status', 'Active')->countAllResults();
        }
        return $this->countAllResults(); // Return all if no status column
    }

    /**
     * Search courses by various fields
     */
    public function searchCourses($searchTerm)
    {
        return $this->select('courses.*, users.name as teacher_name')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->groupStart()
                        ->like('courses.title', $searchTerm)
                        ->orLike('courses.course_code', $searchTerm)
                        ->orLike('users.name', $searchTerm)
                    ->groupEnd()
                    ->findAll();
    }
}
