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

        $user_id = session('user_id');
        $enrollmentModel = new EnrollmentModel();

        // Check if user is already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // Insert new enrollment record
        $enrollmentData = [
            'user_id' => $user_id,
            'course_id' => $course_id
        ];

        $result = $enrollmentModel->enrollUser($enrollmentData);

        if ($result) {
            // Create notification for the user
            $courseModel = new CourseModel();
            $course = $courseModel->find($course_id);
            if ($course) {
                $notificationModel = new NotificationModel();
                $notificationModel->insert([
                    'user_id' => $user_id,
                    'message' => 'You have been enrolled in ' . $course['title'],
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in the course!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll in the course. Please try again.'
            ]);
        }
    }

    /**
     * Display course information (optional method)
     */
    public function index()
    {
        $courseModel = new CourseModel();
        $data['courses'] = $courseModel->findAll();

        return view('courses/index', $data);
    }

    /**
     * Show specific course details (optional method)
     */
    public function show($id)
    {
        $courseModel = new CourseModel();

        if (!$courseModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Course not found');
        }

        $data['course'] = $courseModel->find($id);
        return view('courses/show', $data);
    }

    /**
     * Search courses method
     */
    public function search()
    {
        $courseModel = new CourseModel();

        // Get search query from GET or POST
        $query = $this->request->getGet('query') ?? $this->request->getPost('query') ?? '';

        // Check if request is AJAX
        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('Content-Type') === 'application/json';

        if (!empty($query)) {
            // Use Query Builder with LIKE for search
            $courses = $courseModel->like('title', $query, 'both')
                                   ->orLike('description', $query, 'both')
                                   ->findAll();
        } else {
            // Return all courses if no query
            $courses = $courseModel->findAll();
        }

        if ($isAjax) {
            // Return JSON for AJAX requests
            return $this->response->setJSON(['courses' => $courses]);
        } else {
            // Return view for normal requests
            $data['courses'] = $courses;
            return view('courses/index', $data);
        }
    }


}
