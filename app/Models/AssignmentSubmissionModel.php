<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentSubmissionModel extends Model
{
    protected $table = 'assignment_submissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'assignment_id',
        'student_id',
        'file_path',
        'submission_text',
        'submission_date',
        'status',
        'grade',
        'feedback',
        'graded_at',
        'graded_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'assignment_id' => 'required|integer',
        'student_id' => 'required|integer'
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get submission by assignment and student
     */
    public function getSubmission($assignmentId, $studentId)
    {
        return $this->where('assignment_id', $assignmentId)
                    ->where('student_id', $studentId)
                    ->first();
    }

    /**
     * Check if student has already submitted
     */
    public function hasSubmitted($assignmentId, $studentId)
    {
        return $this->where('assignment_id', $assignmentId)
                    ->where('student_id', $studentId)
                    ->countAllResults() > 0;
    }

    /**
     * Get all submissions for an assignment with student details
     */
    public function getSubmissionsByAssignment($assignmentId)
    {
        return $this->select('assignment_submissions.*, users.name as student_name, users.email as student_email')
                    ->join('users', 'users.id = assignment_submissions.student_id')
                    ->where('assignment_submissions.assignment_id', $assignmentId)
                    ->orderBy('assignment_submissions.submission_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get all enrolled students and their submission status for an assignment
     */
    public function getAssignmentSubmissionStatus($assignmentId, $courseId)
    {
        $db = \Config\Database::connect();
        
        // Get all enrolled students
        $query = $db->query("
            SELECT 
                u.id as student_id,
                u.name as student_name,
                u.email as student_email,
                asub.id as submission_id,
                asub.file_path,
                asub.submission_text,
                asub.submission_date,
                asub.status,
                asub.grade,
                asub.feedback,
                asub.graded_at
            FROM enrollments e
            INNER JOIN users u ON u.id = e.user_id
            LEFT JOIN assignment_submissions asub ON asub.assignment_id = ? AND asub.student_id = u.id
            WHERE e.course_id = ? AND u.role = 'student'
            ORDER BY u.name ASC
        ", [$assignmentId, $courseId]);
        
        return $query->getResultArray();
    }

    /**
     * Submit assignment
     */
    public function submitAssignment($data)
    {
        $data['submission_date'] = date('Y-m-d H:i:s');
        $data['status'] = 'Submitted';
        return $this->insert($data);
    }

    /**
     * Grade submission
     */
    public function gradeSubmission($submissionId, $grade, $feedback, $gradedBy)
    {
        $data = [
            'grade' => $grade,
            'feedback' => $feedback,
            'graded_at' => date('Y-m-d H:i:s'),
            'graded_by' => $gradedBy,
            'status' => 'Graded'
        ];
        
        return $this->update($submissionId, $data);
    }

    /**
     * Get student's submissions for a course
     */
    public function getStudentSubmissions($studentId, $courseId)
    {
        return $this->select('assignment_submissions.*, assignments.title as assignment_title, assignments.due_date')
                    ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
                    ->where('assignment_submissions.student_id', $studentId)
                    ->where('assignments.course_id', $courseId)
                    ->orderBy('assignment_submissions.submission_date', 'DESC')
                    ->findAll();
    }

    /**
     * Delete submission and its file if exists
     */
    public function deleteSubmission($id)
    {
        $submission = $this->find($id);
        if ($submission && !empty($submission['file_path'])) {
            $filePath = WRITEPATH . 'uploads/submissions/' . $submission['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        return $this->delete($id);
    }
}

