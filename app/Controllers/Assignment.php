<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AssignmentModel;
use App\Models\AssignmentSubmissionModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;

class Assignment extends BaseController
{
    protected $assignmentModel;
    protected $submissionModel;
    protected $courseModel;

    public function __construct()
    {
        $this->assignmentModel = new AssignmentModel();
        $this->submissionModel = new AssignmentSubmissionModel();
        $this->courseModel = new CourseModel();
    }

    /**
     * Teacher: View assignments for a course
     */
    public function teacherView($courseId)
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['teacher', 'admin'])) {
            return redirect()->to('/login')->with('error', 'Access denied');
        }

        $course = $this->courseModel->find($courseId);
        
        // Verify teacher owns this course
        if (session()->get('role') === 'teacher' && $course['instructor_id'] != session()->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        $assignments = $this->assignmentModel->getAssignmentsByCourse($courseId);

        return view('assignments/teacher_view', [
            'course' => $course,
            'assignments' => $assignments
        ]);
    }

    /**
     * Teacher: Create assignment form
     */
    public function create($courseId)
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['teacher', 'admin'])) {
            return redirect()->to('/login')->with('error', 'Access denied');
        }

        $course = $this->courseModel->find($courseId);
        
        // Verify teacher owns this course
        if (session()->get('role') === 'teacher' && $course['instructor_id'] != session()->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        return view('assignments/create', [
            'course' => $course
        ]);
    }

    /**
     * Teacher: Store new assignment
     */
    public function store()
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['teacher', 'admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $rules = [
            'course_id' => 'required|integer',
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'required',
            'due_date' => 'permit_empty|valid_date',
            'assignment_file' => 'permit_empty|uploaded[assignment_file]|max_size[assignment_file,10240]|ext_in[assignment_file,pdf,doc,docx,ppt,pptx,txt,zip]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $courseId = $this->request->getPost('course_id');
        $course = $this->courseModel->find($courseId);
        
        // Verify teacher owns this course
        if (session()->get('role') === 'teacher' && $course['instructor_id'] != session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Handle file upload
        $fileName = null;
        $file = $this->request->getFile('assignment_file');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/assignments/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $fileName);
        }

        $data = [
            'course_id' => $courseId,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'due_date' => $this->request->getPost('due_date') ?: null,
            'file_attachment' => $fileName,
            'created_by' => session()->get('user_id')
        ];

        $assignmentId = $this->assignmentModel->insert($data);

        if ($assignmentId) {
            // Notify all enrolled students
            $enrollmentModel = new EnrollmentModel();
            $enrolledStudents = $enrollmentModel->where('course_id', $courseId)->findAll();
            
            $notificationModel = new NotificationModel();
            foreach ($enrolledStudents as $enrollment) {
                $notificationModel->insert([
                    'user_id' => $enrollment['user_id'],
                    'message' => 'New assignment posted: ' . $data['title'],
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Assignment created successfully',
                'assignment_id' => $assignmentId
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to create assignment']);
        }
    }

    /**
     * Student: View assignments for a course
     */
    public function studentView($courseId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return redirect()->to('/login')->with('error', 'Access denied');
        }

        // Verify student is enrolled in this course
        $enrollmentModel = new EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled(session()->get('user_id'), $courseId)) {
            return redirect()->to('/dashboard')->with('error', 'You are not enrolled in this course');
        }

        $course = $this->courseModel->find($courseId);
        $assignments = $this->assignmentModel->getAssignmentsWithCourse($courseId);
        
        // Get submission status for each assignment
        foreach ($assignments as &$assignment) {
            $submission = $this->submissionModel->getSubmission($assignment['id'], session()->get('user_id'));
            $assignment['submission'] = $submission;
            $assignment['has_submitted'] = !empty($submission);
        }

        return view('assignments/student_view', [
            'course' => $course,
            'assignments' => $assignments
        ]);
    }

    /**
     * Student: Submit assignment form
     */
    public function submitForm($assignmentId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return redirect()->to('/login')->with('error', 'Access denied');
        }

        $assignment = $this->assignmentModel->getAssignmentDetails($assignmentId);
        
        if (!$assignment) {
            return redirect()->to('/dashboard')->with('error', 'Assignment not found');
        }

        // Verify student is enrolled in the course
        $enrollmentModel = new EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled(session()->get('user_id'), $assignment['course_id'])) {
            return redirect()->to('/dashboard')->with('error', 'You are not enrolled in this course');
        }

        // Check if already submitted
        $existingSubmission = $this->submissionModel->getSubmission($assignmentId, session()->get('user_id'));
        
        return view('assignments/submit', [
            'assignment' => $assignment,
            'existingSubmission' => $existingSubmission
        ]);
    }

    /**
     * Student: Submit assignment
     */
    public function submit()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $rules = [
            'assignment_id' => 'required|integer',
            'submission_text' => 'permit_empty',
            'submission_file' => 'permit_empty|uploaded[submission_file]|max_size[submission_file,10240]|ext_in[submission_file,pdf,doc,docx,ppt,pptx,txt,zip,jpg,jpeg,png]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $assignmentId = $this->request->getPost('assignment_id');
        $assignment = $this->assignmentModel->find($assignmentId);
        
        if (!$assignment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found']);
        }

        // Verify student is enrolled
        $enrollmentModel = new EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled(session()->get('user_id'), $assignment['course_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'You are not enrolled in this course']);
        }

        // Check if already submitted
        if ($this->submissionModel->hasSubmitted($assignmentId, session()->get('user_id'))) {
            return $this->response->setJSON(['success' => false, 'message' => 'You have already submitted this assignment']);
        }

        // Check due date
        if (!empty($assignment['due_date'])) {
            $dueDate = strtotime($assignment['due_date']);
            if (time() > $dueDate) {
                return $this->response->setJSON(['success' => false, 'message' => 'Assignment submission deadline has passed']);
            }
        }

        // Handle file upload
        $fileName = null;
        $file = $this->request->getFile('submission_file');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/submissions/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $fileName);
        }

        $data = [
            'assignment_id' => $assignmentId,
            'student_id' => session()->get('user_id'),
            'file_path' => $fileName,
            'submission_text' => $this->request->getPost('submission_text')
        ];

        if ($this->submissionModel->submitAssignment($data)) {
            // Notify teacher
            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id' => $assignment['created_by'],
                'message' => session()->get('user_name') . ' submitted assignment: ' . $assignment['title'],
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Assignment submitted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to submit assignment']);
        }
    }

    /**
     * Teacher: View submissions for an assignment
     */
    public function viewSubmissions($assignmentId)
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['teacher', 'admin'])) {
            return redirect()->to('/login')->with('error', 'Access denied');
        }

        $assignment = $this->assignmentModel->getAssignmentDetails($assignmentId);
        
        if (!$assignment) {
            return redirect()->to('/dashboard')->with('error', 'Assignment not found');
        }

        // Verify teacher owns this course
        if (session()->get('role') === 'teacher' && $assignment['instructor_id'] != session()->get('user_id')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Get all enrolled students and their submission status
        $submissions = $this->submissionModel->getAssignmentSubmissionStatus($assignmentId, $assignment['course_id']);

        return view('assignments/view_submissions', [
            'assignment' => $assignment,
            'submissions' => $submissions
        ]);
    }

    /**
     * Teacher: Grade submission
     */
    public function grade()
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['teacher', 'admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $rules = [
            'submission_id' => 'required|integer',
            'grade' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'feedback' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $submissionId = $this->request->getPost('submission_id');
        $grade = $this->request->getPost('grade');
        $feedback = $this->request->getPost('feedback');

        $submission = $this->submissionModel->find($submissionId);
        
        if (!$submission) {
            return $this->response->setJSON(['success' => false, 'message' => 'Submission not found']);
        }

        // Verify teacher owns the course
        $assignment = $this->assignmentModel->getAssignmentDetails($submission['assignment_id']);
        if (session()->get('role') === 'teacher' && $assignment['instructor_id'] != session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if ($this->submissionModel->gradeSubmission($submissionId, $grade, $feedback, session()->get('user_id'))) {
            // Notify student
            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id' => $submission['student_id'],
                'message' => 'Your assignment "' . $assignment['title'] . '" has been graded',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Assignment graded successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to grade assignment']);
        }
    }

    /**
     * Download assignment file
     */
    public function downloadAssignment($assignmentId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Access denied');
        }

        $assignment = $this->assignmentModel->find($assignmentId);
        
        if (!$assignment || empty($assignment['file_attachment'])) {
            return redirect()->back()->with('error', 'File not found');
        }

        $filePath = WRITEPATH . 'uploads/assignments/' . $assignment['file_attachment'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found');
        }

        return $this->response->download($filePath, null)->setFileName($assignment['file_attachment']);
    }

    /**
     * Download submission file
     */
    public function downloadSubmission($submissionId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Access denied');
        }

        $submission = $this->submissionModel->find($submissionId);
        
        if (!$submission || empty($submission['file_path'])) {
            return redirect()->back()->with('error', 'File not found');
        }

        // Verify access
        $userId = session()->get('user_id');
        $role = session()->get('role');
        
        if ($role === 'student' && $submission['student_id'] != $userId) {
            return redirect()->back()->with('error', 'Access denied');
        }

        $filePath = WRITEPATH . 'uploads/submissions/' . $submission['file_path'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found');
        }

        return $this->response->download($filePath, null)->setFileName($submission['file_path']);
    }

    /**
     * Delete assignment (teacher only)
     */
    public function delete($assignmentId)
    {
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['teacher', 'admin'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $assignment = $this->assignmentModel->getAssignmentDetails($assignmentId);
        
        if (!$assignment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found']);
        }

        // Verify teacher owns this course
        if (session()->get('role') === 'teacher' && $assignment['instructor_id'] != session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if ($this->assignmentModel->deleteAssignment($assignmentId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Assignment deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete assignment']);
        }
    }
}

