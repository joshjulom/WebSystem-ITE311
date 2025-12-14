<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\NotificationModel;

class Course extends BaseController
{
    /**
     * Handle AJAX enrollment request
     */
    public function enroll()
    {
        // Check if user is logged in
        if (!session()->has('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please log in to enroll in courses.'
            ]);
        }

        // Check if user account is active
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find(session('user_id'));
        if (!$user || ($user['status'] ?? 'active') !== 'active') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Your account has been deactivated.'
            ]);
        }

        $course_id = $this->request->getPost('course_id');

        if (!$course_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID is required.'
            ]);
        }

        // Determine which user is being enrolled. By default it's the current session user.
        // Admins/teachers may pass a 'user_id' POST param to enroll another user.
        $requested_user_id = $this->request->getPost('user_id');
        $user_id = session('user_id');

        // Only admins may enroll another user via POST (teachers cannot enroll other users)
        if (!empty($requested_user_id) && session('role') === 'admin') {
            // Use the provided user id as the target (ensure it's integer)
            $target_user_id = (int) $requested_user_id;
        } else {
            $target_user_id = $user_id;
        }

        $enrollmentModel = new EnrollmentModel();

        // Check if target user is already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($target_user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User is already enrolled in this course.'
            ]);
        }

        // Insert new enrollment record for the target user
        $enrollmentData = [
            'user_id' => $target_user_id,
            'course_id' => $course_id
        ];

        try {
            $enrollmentId = $enrollmentModel->enrollUser($enrollmentData);

            if (!$enrollmentId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to submit enrollment request. Please try again.'
                ]);
            }

            // Enrollment succeeded - now try to send notifications (but don't fail if this part fails)
            try {
                $courseModel = new CourseModel();
                $course = $courseModel->find($course_id);
                
                if ($course) {
                    $notificationModel = new NotificationModel();
                    $userModel = new \App\Models\UserModel();
                    $student = $userModel->find($target_user_id);
                    
                    // Check if new columns exist
                    $db = \Config\Database::connect();
                    $fields = $db->getFieldNames('notifications');
                    $hasNewFields = in_array('type', $fields) && in_array('enrollment_id', $fields);
                    
                    if ($hasNewFields) {
                        // Use new format with type and enrollment_id
                        $notificationModel->insert([
                            'user_id' => $target_user_id,
                            'message' => "Your enrollment request for course '{$course['title']}' has been submitted and is pending approval",
                            'type' => 'enrollment',
                            'enrollment_id' => $enrollmentId,
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);

                        if (!empty($course['instructor_id'])) {
                            $notificationModel->insert([
                                'user_id' => $course['instructor_id'],
                                'message' => ($student['name'] ?? 'A student') . " has requested to enroll in your '{$course['title']}' course",
                                'type' => 'enrollment',
                                'enrollment_id' => $enrollmentId,
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                        } else {
                            // No instructor assigned; instructor notification skipped. Admins will be notified below.
                        }
                    } else {
                        // Use legacy format without new columns
                        $notificationModel->insert([
                            'user_id' => $target_user_id,
                            'message' => "Your enrollment request for course '{$course['title']}' has been submitted and is pending approval",
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);

                        if (!empty($course['instructor_id'])) {
                            $notificationModel->insert([
                                'user_id' => $course['instructor_id'],
                                'message' => ($student['name'] ?? 'A student') . " has requested to enroll in your '{$course['title']}' course",
                                'is_read' => 0,
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                        } else {
                            // No instructor assigned; instructor notification skipped. Admins will be notified below (legacy format).
                        }
                    }
                }

                    // Notify all admins about this enrollment request (exclude instructor if present to avoid duplicate)
                    try {
                        $admins = $userModel->where('role', 'admin')->findAll();
                        foreach ($admins as $admin) {
                            // Skip notifying the instructor here since they were already notified above (if present)
                            if (!empty($course['instructor_id']) && $admin['id'] == $course['instructor_id']) {
                                continue;
                            }

                            if ($hasNewFields) {
                                $notificationModel->insert([
                                    'user_id' => $admin['id'],
                                    'message' => ($student['name'] ?? 'A student') . " has requested to enroll in '{$course['title']}'",
                                    'type' => 'enrollment',
                                    'enrollment_id' => $enrollmentId,
                                    'is_read' => 0,
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);
                            } else {
                                $notificationModel->insert([
                                    'user_id' => $admin['id'],
                                    'message' => ($student['name'] ?? 'A student') . " has requested to enroll in '{$course['title']}'",
                                    'is_read' => 0,
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        // Don't let admin notification failures break the enrollment flow
                        log_message('error', 'Admin notification failed: ' . $e->getMessage());
                    }

            } catch (\Exception $e) {
                // Log the error but don't fail the enrollment
                log_message('error', 'Notification creation failed: ' . $e->getMessage());
                // Continue - enrollment succeeded even if notification failed
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment request submitted! Waiting for teacher approval.'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Enrollment error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred during enrollment: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Approve enrollment request (Teacher/Admin only)
     */
    public function approveEnrollment($enrollment_id)
    {
        if (!session('isLoggedIn') || !in_array(session('role'), ['teacher', 'admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
        }
        
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollment_id);
        
        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment request not found'
            ]);
        }
        
        // Verify teacher owns this course
        if (session('role') === 'teacher') {
            $courseModel = new CourseModel();
            $course = $courseModel->find($enrollment['course_id']);
            
            if ($course['instructor_id'] != session('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You do not have permission to approve this enrollment'
                ]);
            }
        }
        
        if ($enrollmentModel->approveEnrollment($enrollment_id)) {
            // Notify student
            $courseModel = new CourseModel();
            $course = $courseModel->find($enrollment['course_id']);

            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id' => $enrollment['user_id'],
                'message' => 'Your enrollment in "' . ($course['title'] ?? 'the course') . '" has been approved!',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Also notify admins that this enrollment was approved
            try {
                $db = \Config\Database::connect();
                $fields = $db->getFieldNames('notifications');
                $hasNewFields = in_array('type', $fields) && in_array('enrollment_id', $fields);

                $userModel = new \App\Models\UserModel();
                $admins = $userModel->where('role', 'admin')->findAll();
                foreach ($admins as $admin) {
                    // Don't notify the approving teacher if they are also an admin (avoid duplicate for same user)
                    if ($admin['id'] == session('user_id')) {
                        continue;
                    }

                    if ($hasNewFields) {
                        $notificationModel->insert([
                            'user_id' => $admin['id'],
                            'message' => 'Enrollment for "' . ($course['title'] ?? 'the course') . '" was approved by ' . (session('name') ?? 'a teacher'),
                            'type' => 'enrollment',
                            'enrollment_id' => $enrollment_id,
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    } else {
                        $notificationModel->insert([
                            'user_id' => $admin['id'],
                            'message' => 'Enrollment for "' . ($course['title'] ?? 'the course') . '" was approved by ' . (session('name') ?? 'a teacher'),
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Admin notification on approval failed: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment approved successfully'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to approve enrollment'
        ]);
    }
    
    /**
     * Reject enrollment request (Teacher/Admin only)
     */
    public function rejectEnrollment($enrollment_id)
    {
        if (!session('isLoggedIn') || !in_array(session('role'), ['teacher', 'admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
        }
        
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollment_id);
        
        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment request not found'
            ]);
        }
        
        // Verify teacher owns this course
        if (session('role') === 'teacher') {
            $courseModel = new CourseModel();
            $course = $courseModel->find($enrollment['course_id']);
            
            if ($course['instructor_id'] != session('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You do not have permission to reject this enrollment'
                ]);
            }
        }
        
        if ($enrollmentModel->rejectEnrollment($enrollment_id)) {
            // Notify student
            $courseModel = new CourseModel();
            $course = $courseModel->find($enrollment['course_id']);

            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id' => $enrollment['user_id'],
                'message' => 'Your enrollment request for "' . ($course['title'] ?? 'the course') . '" was declined',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment request rejected'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to reject enrollment'
        ]);
    }

    /**
     * Display course information (optional method)
     */
    public function index()
    {
        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();
        $userModel = new \App\Models\UserModel();

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        // Get appropriate courses based on user role
        if ($userId && $userRole === 'student') {
            // For students, show only available courses (not enrolled)
            $data['courses'] = $enrollmentModel->getAvailableCourses($userId);
        } else {
            // For teachers/admin/guest, show all courses
            $data['courses'] = $courseModel->getCoursesWithTeachers();
        }

        // If user is teacher/admin, get enrollment counts for each course
        if ($userId && in_array($userRole, ['admin', 'teacher'])) {
            $enrollmentCounts = [];
            foreach ($data['courses'] as $course) {
                $enrollmentCounts[$course['id']] = [
                    'approved' => $enrollmentModel->getEnrollmentCount($course['id'], 'approved'),
                    'pending' => $enrollmentModel->getEnrollmentCount($course['id'], 'pending')
                ];
            }
            $data['enrollmentCounts'] = $enrollmentCounts;
        }

        // If user is admin, get teachers list for modal
        if ($userId && $userRole === 'admin') {
            $data['teachers'] = $userModel->where('role', 'teacher')->findAll();
        }

        return view('courses/index', $data);
    }

    /**
     * Show student's enrolled courses (Student only)
     */
    public function myCourses()
    {
        // Check if user is logged in and is student
        if (!session()->has('user_id') || session()->get('role') !== 'student') {
            return redirect()->to('/courses')->with('error', 'Access denied. Only students can view enrolled courses.');
        }

        $user_id = session('user_id');
        $enrollmentModel = new EnrollmentModel();
        $materialModel = new \App\Models\MaterialModel();
        $assignmentModel = new \App\Models\AssignmentModel();
        $submissionModel = new \App\Models\AssignmentSubmissionModel();

        $enrolledCourses = $enrollmentModel->getUserEnrollments($user_id);

        // Add materials information to courses (rebuild array cleanly to avoid reference issues)
        $processedCourses = [];
        foreach ($enrolledCourses as $course) {
            $course['materials'] = $materialModel->getMaterialsByCourse($course['course_id']);
            $processedCourses[] = $course;
        }
        $data['enrolledCourses'] = $processedCourses;

        // Calculate student statistics
        $stats = [
            'upcomingDeadlines' => 0,
            'recentGrade' => 'N/A',
            'overallProgress' => 0,
            'totalAssignments' => 0
        ];

        if (!empty($data['enrolledCourses'])) {
            $totalAssignments = 0;
            $completedAssignments = 0;
            $upcomingDeadlines = 0;
            $recentGrades = [];

            foreach ($data['enrolledCourses'] as $course) {
                // Get assignments for this course
                $assignments = $assignmentModel->where('course_id', $course['course_id'])
                                              ->findAll();

                foreach ($assignments as $assignment) {
                    $totalAssignments++;

                    // Check for upcoming deadlines (next 7 days)
                    if (!empty($assignment['due_date'])) {
                        $dueDate = strtotime($assignment['due_date']);
                        $now = time();
                        $oneWeekFromNow = strtotime('+7 days', $now);

                        if ($dueDate >= $now && $dueDate <= $oneWeekFromNow) {
                            $upcomingDeadlines++;
                        }
                    }

                    // Get submission and grade for recent grades
                    $submission = $submissionModel->getSubmission($assignment['id'], $user_id);
                    if ($submission) {
                        $completedAssignments++;
                        if ($submission['status'] === 'Graded' && !empty($submission['grade'])) {
                            $recentGrades[] = (int)$submission['grade'];
                        }
                    }
                }
            }

            $stats['upcomingDeadlines'] = $upcomingDeadlines;
            $stats['totalAssignments'] = $totalAssignments;

            // Calculate recent grade (latest graded assignment)
            if (!empty($recentGrades)) {
                rsort($recentGrades); // Sort descending to get highest recent grade
                $stats['recentGrade'] = $recentGrades[0] . '/100';
            }

            // Calculate overall progress (completed/total assignments)
            if ($totalAssignments > 0) {
                $stats['overallProgress'] = round(($completedAssignments / $totalAssignments) * 100);
            }
        }

        $data['stats'] = $stats;

        return view('courses/my_courses', $data);
    }

    /**
     * Show specific course details with enrolled students (Teacher/Admin only)
     */
    public function show($id)
    {
        // Check if user is logged in and is teacher/admin
        if (!session()->has('user_id') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return redirect()->to('/course')->with('error', 'Access denied. Only teachers and admins can view enrollment details.');
        }

        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();

        $course = $courseModel->find($id);
        
        if (!$course) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Course not found');
        }

        // For teachers, verify they own this course
        if (session()->get('role') === 'teacher' && $course['instructor_id'] != session()->get('user_id')) {
            return redirect()->to('/course')->with('error', 'You can only view enrollment details for your own courses.');
        }

        $data['course'] = $course;
        // If the current user is the teacher for this course, include both approved and teacher_unenrolled entries
        if (session()->has('user_id') && session('role') === 'teacher' && $course['instructor_id'] == session('user_id')) {
            $data['enrolledStudents'] = $enrollmentModel->select('enrollments.*, users.name as student_name, users.email as student_email')
                                               ->join('users', 'users.id = enrollments.user_id')
                                               ->where('enrollments.course_id', $id)
                                               ->whereIn('enrollments.status', ['approved','teacher_unenrolled'])
                                               ->orderBy('enrollments.enrollment_date', 'DESC')
                                               ->findAll();
        } else {
            // Regular behavior: only approved enrollments
            $data['enrolledStudents'] = $enrollmentModel->getEnrolledStudents($id);
        }

        $data['pendingRequests'] = $enrollmentModel->select('enrollments.*, users.name as student_name, users.email as student_email')
                                                    ->join('users', 'users.id = enrollments.user_id')
                                                    ->where('enrollments.course_id', $id)
                                                    ->where('enrollments.status', 'pending')
                                                    ->findAll();
        
        return view('courses/show', $data);
    }

    /**
     * Search courses method
     */
    public function search()
    {
        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();

        // Get search query from GET or POST
        $query = $this->request->getGet('query') ?? $this->request->getPost('query') ?? '';

        // Check if request is AJAX
        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('Content-Type') === 'application/json';

        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        // Get appropriate courses based on user role
        if ($userId && $userRole === 'student') {
            // For students, search from available courses
            if (!empty($query)) {
                // Need to do search on available courses - first get available, then filter by query
                $availableCourses = $enrollmentModel->getAvailableCourses($userId);
                $courses = array_filter($availableCourses, function($course) use ($query) {
                    return stripos($course['title'], $query) !== false || stripos($course['description'], $query) !== false;
                });
            } else {
                $courses = $enrollmentModel->getAvailableCourses($userId);
            }
        } else {
            // For teachers/admin/guest, search from all courses
            if (!empty($query)) {
                // Use Query Builder with LIKE for search
                $courses = $courseModel->like('title', $query, 'both')
                                       ->orLike('description', $query, 'both')
                                       ->findAll();
            } else {
                // Return all courses if no query
                $courses = $courseModel->getCoursesWithTeachers();
            }
        }

        if ($isAjax) {
            // Return JSON for AJAX requests
            return $this->response->setJSON(['courses' => array_values($courses)]);
        } else {
            // Return view for normal requests
            $data['courses'] = array_values($courses);
            // If user is teacher/admin, get enrollment counts for each course
            if ($userId && in_array($userRole, ['admin', 'teacher'])) {
                $enrollmentCounts = [];
                foreach ($data['courses'] as $course) {
                    $enrollmentCounts[$course['id']] = [
                        'approved' => $enrollmentModel->getEnrollmentCount($course['id'], 'approved'),
                        'pending' => $enrollmentModel->getEnrollmentCount($course['id'], 'pending')
                    ];
                }
                $data['enrollmentCounts'] = $enrollmentCounts;
            }
            return view('courses/index', $data);
        }
    }

    /**
     * Create new course
     */
    public function create()
    {
        // Only admins can create courses
        if (!session()->has('user_id') || session()->get('role') !== 'admin') {
            return redirect()->to('/courses')->with('error', 'Access denied');
        }

        return view('courses/create');
    }

    /**
     * Store new course
     */
    public function store()
    {
        // Only admins can store/create courses
        if (!session()->has('user_id') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $courseModel = new CourseModel();

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Validation failed', 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'schedule' => $this->request->getPost('schedule'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'status' => $this->request->getPost('status') ?: 'Active',
            'instructor_id' => session()->get('user_id')
        ];

        if ($courseModel->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Course created successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to create course']);
        }
    }

    /**
     * Edit course
     */
    public function edit($id)
    {
        // Check if user is admin or teacher
        if (!session()->has('user_id') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return redirect()->to('/login')->with('error', 'Access denied');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($id);

        if (!$course) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Course not found');
        }

        $data['course'] = $course;
        return view('courses/edit', $data);
    }

    /**
     * Update course
     */
    public function update($id)
    {
        // Check if user is admin or teacher
        if (!session()->has('user_id') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $courseModel = new CourseModel();

        if (!$courseModel->find($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found']);
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Validation failed', 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'schedule' => $this->request->getPost('schedule'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'status' => $this->request->getPost('status') ?: 'Active'
        ];

        if ($courseModel->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Course updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update course']);
        }
    }

    /**
     * Delete course
     */
    public function delete($id)
    {
        // Check if user is admin or teacher
        if (!session()->has('user_id') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $courseModel = new CourseModel();

        if (!$courseModel->find($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found']);
        }

        if ($courseModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Course deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete course']);
        }
    }
    
    /**
     * Remove rejected enrollment (Student only)
     */
    public function removeRejectedEnrollment($enrollment_id)
    {
        if (!session('isLoggedIn') || session('role') !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollment_id);

        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment not found'
            ]);
        }

        // Verify the enrollment belongs to the current user
        if ($enrollment['user_id'] != session('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to remove this enrollment'
            ]);
        }

        // Verify the enrollment is rejected
        if ($enrollment['status'] !== 'rejected') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only rejected enrollments can be removed'
            ]);
        }

        if ($enrollmentModel->cleanupRejectedEnrollment($enrollment_id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rejected enrollment removed successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to remove enrollment'
        ]);
    }

    /**
     * Assign teacher to course (Admin only)
     */
    public function assignTeacher($courseId)
    {
        // Check if user is admin
        if (!session()->has('user_id') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        $courseModel = new CourseModel();
        $userModel = new \App\Models\UserModel();

        $course = $courseModel->find($courseId);

        if (!$course) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Course not found');
        }

        $data['course'] = $course;
        $data['teachers'] = $userModel->where('role', 'teacher')->findAll();

        // Handle POST request
        if ($this->request->getMethod() === 'POST') {
            $teacherId = $this->request->getPost('teacher_id');
            $semester = $this->request->getPost('semester');
            $academicYear = $this->request->getPost('academic_year');
            $maxStudents = $this->request->getPost('max_students');
            $scheduleArray = $this->request->getPost('schedule') ?? [];
            $startTime = $this->request->getPost('start_time');
            $endTime = $this->request->getPost('end_time');

            // Schedule as comma-separated string
            $schedule = empty($scheduleArray) ? null : implode(',', $scheduleArray);

            $updateData = [
                'semester' => $semester,
                'school_year' => $academicYear,
                'max_students' => $maxStudents,
                'schedule' => $schedule,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'instructor_id' => empty($teacherId) ? null : $teacherId
            ];

            try {
                if ($courseModel->update($courseId, $updateData)) {
                    $successMessage = !empty($teacherId) ? 'Teacher assigned and course updated successfully!' : 'Teacher removed and course updated successfully!';
                    $data['success'] = $successMessage;

                    // Re-fetch course data
                    $data['course'] = $courseModel->find($courseId);
                    return view('courses/assign_teacher', $data);
                } else {
                    $data['error'] = 'Failed to update course. Please try again.';
                    return view('courses/assign_teacher', $data);
                }
            } catch (\Exception $e) {
                $data['error'] = 'An error occurred: ' . $e->getMessage();
                return view('courses/assign_teacher', $data);
            }
        }

        return view('courses/assign_teacher', $data);
    }

    /**
     * Update teacher assignment via AJAX (Admin only)
     */
    public function updateTeacher($courseId)
    {
        // Check if user is admin
        if (!session()->has('user_id') || session()->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied. Admin only.'
            ]);
        }

        $courseModel = new CourseModel();
        $teacherId = $this->request->getPost('teacher_id');

        if (!$teacherId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select a teacher.'
            ]);
        }

        if ($courseModel->update($courseId, ['instructor_id' => $teacherId])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Teacher assigned successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to assign teacher. Please try again.'
            ]);
        }
    }

    /**
     * Manage students in course (Admin only)
     */
    public function manageStudents($courseId)
    {
        // Check if user is admin
        if (!session()->has('user_id') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin only.');
        }

        $courseModel = new CourseModel();
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $userModel = new \App\Models\UserModel();

        $course = $courseModel->find($courseId);

        if (!$course) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Course not found');
        }

        $data['course'] = $course;
    $data['enrolledStudents'] = $enrollmentModel->getEnrolledStudents($courseId);
    $data['pendingRequests'] = $enrollmentModel->getPendingRequestsForCourse($courseId);
    // Teacher-initiated unenrollments that require admin review
    $data['teacherUnenrolled'] = $enrollmentModel->select('enrollments.*, users.name as student_name, users.email as student_email')
                            ->join('users', 'users.id = enrollments.user_id')
                            ->where('enrollments.course_id', $courseId)
                            ->where('enrollments.status', 'teacher_unenrolled')
                            ->orderBy('enrollments.enrollment_date', 'DESC')
                            ->findAll();
        $data['allStudents'] = $userModel->where('role', 'student')->findAll();

        return view('courses/manage_students', $data);
    }

    /**
     * Remove student from course (Admin only)
     */
    public function removeStudent($enrollmentId)
    {
        // Check if user is admin or teacher who owns the course
        if (!session()->has('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied. Login required.'
            ]);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollmentId);

        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment not found'
            ]);
        }

        // If requester is a teacher, ensure they own the course
        if (session()->get('role') === 'teacher') {
            // Teachers can mark a student as unenrolled but this requires admin review.
            $courseModel = new CourseModel();
            $course = $courseModel->find($enrollment['course_id']);
            if (!$course || $course['instructor_id'] != session('user_id')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied. You do not own this course.'
                ]);
            }

            // Update enrollment status to require admin review
            if ($enrollmentModel->update($enrollmentId, ['status' => 'teacher_unenrolled'])) {
                // Notify all admins about this teacher-initiated unenroll
                try {
                    $userModel = new \App\Models\UserModel();
                    $notificationModel = new NotificationModel();
                    $admins = $userModel->where('role', 'admin')->findAll();

                    $student = $userModel->find($enrollment['user_id']);
                    $teacherName = session('name') ?? 'A teacher';
                    foreach ($admins as $admin) {
                        $notificationModel->insert([
                            'user_id' => $admin['id'],
                            'message' => ($teacherName) . " has unenrolled " . ($student['name'] ?? 'a student') . " from '{$course['title']}' and awaits admin action.",
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Admin notification on teacher unenroll failed: ' . $e->getMessage());
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Student marked for admin review. An admin will need to re-enroll or delete the record.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to mark student for admin review.'
                ]);
            }
        } elseif (session()->get('role') === 'admin') {
            // Admin may permanently remove the enrollment
            if ($enrollmentModel->delete($enrollmentId)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Student removed from course successfully!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to remove student from course.'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ]);
        }
    }

}
