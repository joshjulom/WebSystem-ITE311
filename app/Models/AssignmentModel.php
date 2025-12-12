<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'course_id',
        'title',
        'description',
        'due_date',
        'file_attachment',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'course_id' => 'required|integer',
        'title' => 'required|min_length[3]|max_length[255]',
        'created_by' => 'required|integer'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get all assignments for a specific course
     */
    public function getAssignmentsByCourse($courseId)
    {
        return $this->where('course_id', $courseId)
                    ->orderBy('due_date', 'ASC')
                    ->findAll();
    }

    /**
     * Get assignments created by a specific teacher
     */
    public function getAssignmentsByTeacher($teacherId)
    {
        return $this->select('assignments.*, courses.title as course_title')
                    ->join('courses', 'courses.id = assignments.course_id')
                    ->where('assignments.created_by', $teacherId)
                    ->orderBy('assignments.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get assignments with course information
     */
    public function getAssignmentsWithCourse($courseId)
    {
        return $this->select('assignments.*, courses.title as course_title, users.name as teacher_name')
                    ->join('courses', 'courses.id = assignments.course_id')
                    ->join('users', 'users.id = assignments.created_by', 'left')
                    ->where('assignments.course_id', $courseId)
                    ->orderBy('assignments.due_date', 'ASC')
                    ->findAll();
    }

    /**
     * Get assignment with course and teacher details
     */
    public function getAssignmentDetails($assignmentId)
    {
        return $this->select('assignments.*, courses.title as course_title, courses.instructor_id, users.name as teacher_name')
                    ->join('courses', 'courses.id = assignments.course_id')
                    ->join('users', 'users.id = assignments.created_by', 'left')
                    ->where('assignments.id', $assignmentId)
                    ->first();
    }

    /**
     * Check if user has access to this assignment (either teacher or enrolled student)
     */
    public function canUserAccess($assignmentId, $userId, $role)
    {
        $assignment = $this->find($assignmentId);
        if (!$assignment) {
            return false;
        }

        // If teacher, check if they created it
        if ($role === 'teacher') {
            return $assignment['created_by'] == $userId;
        }

        // If student, check if they're enrolled in the course
        if ($role === 'student') {
            $enrollmentModel = new \App\Models\EnrollmentModel();
            return $enrollmentModel->isAlreadyEnrolled($userId, $assignment['course_id']);
        }

        // Admin has access to everything
        if ($role === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Delete assignment and its file if exists
     */
    public function deleteAssignment($id)
    {
        $assignment = $this->find($id);
        if ($assignment && !empty($assignment['file_attachment'])) {
            $filePath = WRITEPATH . 'uploads/assignments/' . $assignment['file_attachment'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        return $this->delete($id);
    }
}

