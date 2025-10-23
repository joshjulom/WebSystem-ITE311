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


}
