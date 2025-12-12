<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'course_id',
        'enrollment_date',
        'status'
    ];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'course_id' => 'required|integer'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Insert a new enrollment record (as pending by default)
     */
    public function enrollUser($data)
    {
        $data['enrollment_date'] = date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? 'pending'; // Default to pending
        return $this->insert($data);
    }

    /**
     * Fetch all approved courses a user is enrolled in with teacher information
     */
    public function getUserEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.title, courses.description, courses.course_code, courses.schedule, courses.school_year, courses.semester, users.name as teacher_name, users.email as teacher_email')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->join('users', 'users.id = courses.instructor_id', 'left')
                    ->where('enrollments.user_id', $user_id)
                    ->where('enrollments.status', 'approved')
                    ->findAll();
    }
    
    /**
     * Fetch all pending enrollment requests for a user
     */
    public function getPendingEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.title, courses.description')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->where('enrollments.status', 'pending')
                    ->findAll();
    }

    /**
     * Check if a user is already enrolled in a specific course
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->countAllResults() > 0;
    }

    /**
     * Get all available courses (not enrolled or pending by user)
     */
    public function getAvailableCourses($user_id)
    {
        $enrolled_course_ids = $this->select('course_id')
                                   ->where('user_id', $user_id)
                                   ->whereIn('status', ['approved', 'pending'])
                                   ->findAll();

        $enrolled_ids = array_column($enrolled_course_ids, 'course_id');

        $courseModel = new \App\Models\CourseModel();

        if (empty($enrolled_ids)) {
            return $courseModel->findAll();
        }

        return $courseModel->whereNotIn('id', $enrolled_ids)->findAll();
    }
    
    /**
     * Get pending enrollment requests for teacher's courses
     */
    public function getPendingRequestsForTeacher($teacher_id)
    {
        return $this->select('enrollments.*, courses.title as course_title, users.name as student_name, users.email as student_email')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->where('courses.instructor_id', $teacher_id)
                    ->where('enrollments.status', 'pending')
                    ->orderBy('enrollments.enrollment_date', 'DESC')
                    ->findAll();
    }
    
    /**
     * Approve enrollment request
     */
    public function approveEnrollment($enrollment_id)
    {
        return $this->update($enrollment_id, ['status' => 'approved']);
    }
    
    /**
     * Reject enrollment request
     */
    public function rejectEnrollment($enrollment_id)
    {
        return $this->update($enrollment_id, ['status' => 'rejected']);
    }
    
    /**
     * Get rejected enrollments for a user
     */
    public function getRejectedEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.title, courses.description')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->where('enrollments.status', 'rejected')
                    ->orderBy('enrollments.enrollment_date', 'DESC')
                    ->findAll();
    }
    
    /**
     * Delete rejected enrollment (cleanup)
     */
    public function cleanupRejectedEnrollment($enrollment_id)
    {
        return $this->delete($enrollment_id);
    }
    
    /**
     * Get enrolled students for a specific course
     */
    public function getEnrolledStudents($course_id)
    {
        return $this->select('enrollments.*, users.name as student_name, users.email as student_email')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->where('enrollments.course_id', $course_id)
                    ->where('enrollments.status', 'approved')
                    ->findAll();
    }
    
    /**
     * Get enrollment count for a course
     */
    public function getEnrollmentCount($course_id, $status = 'approved')
    {
        return $this->where('course_id', $course_id)
                    ->where('status', $status)
                    ->countAllResults();
    }
    
    /**
     * Get all enrollments for teacher's courses with student details
     */
    public function getEnrollmentsForTeacherCourses($teacher_id)
    {
        return $this->select('enrollments.course_id, COUNT(enrollments.id) as student_count')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('courses.instructor_id', $teacher_id)
                    ->where('enrollments.status', 'approved')
                    ->groupBy('enrollments.course_id')
                    ->findAll();
    }
}
