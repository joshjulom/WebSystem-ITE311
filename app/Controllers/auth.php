<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LoginLogModel;
use App\Models\CourseModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
	public function register()
	{
		helper(['form']);
		$session = session();
		$model = new UserModel();
		
		if ($this->request->getMethod() === 'POST') {
			// Add detailed logging
			log_message('info', 'Registration POST request received');
			log_message('info', 'POST data: ' . print_r($this->request->getPost(), true));
			
			$rules = [
				'name' => 'required|min_length[3]|max_length[100]|regex_match[/^[A-Za-zÑñ ]+$/]',
				// Note: we remove CodeIgniter's `valid_email` rule because many browsers/PHP
				// validators do not accept non-ASCII local-parts (like 'ñ'). We rely on the
				// explicit regex below (which allows Ñ/ñ) and `is_unique` to validate emails.
				'email' => 'required|is_unique[users.email]|regex_match[/^[A-Za-z0-9._%+\-Ññ]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/]',
				'password' => 'required|min_length[6]',
				'password_confirm' => 'matches[password]'
			];

			$messages = [
				'name' => [
					'required' => 'Name is required.',
					'min_length' => 'Name must be at least 3 characters.',
					'max_length' => 'Name cannot exceed 100 characters.',
					'regex_match' => 'Name can only contain letters, spaces, and the Ñ/ñ character. No other special characters are allowed.'
				],
				'email' => [
					'required' => 'Email is required.',
					'valid_email' => 'Please enter a valid email address.',
					'is_unique' => 'This email address is already registered.',
					'regex_match' => 'Email contains invalid characters. Only letters, numbers and . _ % + - are allowed before the @, and a normal domain after it.'
				]
			];

			if ($this->validate($rules, $messages)) {
				log_message('info', 'Validation passed');
				
				try {
					// Get the data from form
					$name = trim($this->request->getPost('name'));
					$email = $this->request->getPost('email');
					
					// Determine role from POST with whitelist and normalization
					$requestedRole = strtolower(trim((string) $this->request->getPost('role')));
					$roleMap = [
						'instructor' => 'teacher',
						'teacher' => 'teacher',
						'admin' => 'admin',
						'student' => 'student',
					];
					$role = $roleMap[$requestedRole] ?? 'student';

					$data = [
						'name' => $name,
						'email' => $email,
						'password' => $this->request->getPost('password'), // Let model handle hashing
						'role' => $role
					];
					
					log_message('info', 'Attempting to insert user data: ' . print_r($data, true));
					
					// Save user to database
					$insertResult = $model->insert($data);
					
					if ($insertResult) {
						log_message('info', 'User inserted successfully with ID: ' . $insertResult);
						$session->setFlashdata('register_success', 'Registration successful. Please login.');
						return redirect()->to(base_url('login'));
					} else {
						// Get the last error for debugging
						$errors = $model->errors();
						$errorMessage = 'Registration failed. ';
						
						log_message('error', 'Model insert failed. Errors: ' . print_r($errors, true));
						log_message('error', 'Model validation errors: ' . print_r($model->getValidationMessages(), true));
						
						if (!empty($errors)) {
							$errorMessage .= implode(', ', $errors);
						} else {
							$errorMessage .= 'Please try again.';
						}
						$session->setFlashdata('register_error', $errorMessage);
					}
				} catch (\Exception $e) {
					log_message('error', 'Registration exception: ' . $e->getMessage());
					log_message('error', 'Stack trace: ' . $e->getTraceAsString());
					$session->setFlashdata('register_error', 'Registration failed. Please try again. Error: ' . $e->getMessage());
				}
			} else {
				// Validation failed
				$validationErrors = $this->validator->getErrors();
				log_message('error', 'Validation failed: ' . print_r($validationErrors, true));
				
				$errorMessage = 'Validation failed: ' . implode(', ', $validationErrors);
				$session->setFlashdata('register_error', $errorMessage);
			}
		}
		
		return view('auth/register', [
			'validation' => $this->validator
		]);
	}

	public function login()
	{
		helper(['form']);
		$session = session();
		
		if ($this->request->getMethod() === 'POST') {
			$rules = [
				'email' => 'required|valid_email',
				'password' => 'required'
			];
			
			if ($this->validate($rules)) {
				$email = $this->request->getPost('email');
				$password = $this->request->getPost('password');
				
				try {
					$model = new UserModel();
					
					// Find user by email only
					$user = $model->where('email', $email)->first();
					
					if ($user && password_verify($password, $user['password'])) {
						// Check if user account is active
						if (($user['status'] ?? 'active') !== 'active') {
							$session->setFlashdata('login_error', 'Your account has been deactivated. Please contact an administrator.');
							return redirect()->to('login');
						}

						// Use the name field directly from database
						$userName = $user['name'] ?? $user['email'];

					// Set session data
						$sessionData = [
							'user_id' => $user['id'],
							'user_name' => $userName,
							'user_email' => $user['email'],
							'role' => $user['role'] ?? 'student',
							'isLoggedIn' => true
						];

						// Log successful login
						try {
							$loginLogModel = new LoginLogModel();
							$loginLogModel->logLogin(
								(int) $user['id'],
								(string) $userName,
								(string) ($user['email'] ?? ''),
								(string) ($user['role'] ?? 'student')
							);
						} catch (\Throwable $e) {
							log_message('error', 'Failed to log login event: ' . $e->getMessage());
						}

						$session->set($sessionData);
						$session->setFlashdata('success', 'Welcome, ' . $userName . '!');

						return redirect()->to('dashboard');
					} else {
						$session->setFlashdata('login_error', 'Invalid email or password.');
					}
				} catch (\Exception $e) {
					log_message('error', 'Login exception: ' . $e->getMessage());
					$session->setFlashdata('login_error', 'Login failed. Please try again.');
				}
			} else {
				$session->setFlashdata('login_error', 'Please check your input and try again.');
			}
		}
		
		return view('auth/login', [
			'validation' => $this->validator
		]);
	}

	public function logout()
	{
		$session = session();
		$session->destroy();
		return redirect()->to('login');
	}

	public function dashboard()
	{
		$session = session();

		// Authorization: ensure user logged in
		if (!$session->get('isLoggedIn')) {
			$session->setFlashdata('login_error', 'Please login to access the dashboard.');
			return redirect()->to('login');
		}

		// Check if user account is active
		$userModel = new UserModel();
		$user = $userModel->find($session->get('user_id'));
		if (!$user || ($user['status'] ?? 'active') !== 'active') {
			$session->destroy();
			$session->setFlashdata('login_error', 'Your account has been deactivated.');
			return redirect()->to('login');
		}

		$role = $session->get('role');
		// Normalize role aliases
		if ($role === 'instructor') {
			$role = 'teacher';
		}
		$adminData = null;
		$teacherData = null;
		$studentData = null;
		
		// Load role-specific data (admin)
		if ($role === 'admin') {
			try {
				$db = \Config\Database::connect();
				$courseModelForAdmin = new CourseModel();
				$userModelForTeachers = new UserModel();
				
				// Check if status column exists
				$statusExists = $db->fieldExists('status', 'courses');
				
				// Get total courses
				$totalCourses = $courseModelForAdmin->countAllResults();
				
				// Get active courses (only if status column exists)
				if ($statusExists) {
					$activeCourses = $courseModelForAdmin->where('status', 'Active')->countAllResults();
				} else {
					$activeCourses = $totalCourses; // All courses are considered active if no status column
				}
				
				// Get all courses with teacher names and enrollment counts
				$courses = $courseModelForAdmin->getCoursesWithTeachersAndEnrollments();
				
				$adminData = [
					'totalUsers' => model(UserModel::class)->countAllResults(),
					'latestUsers' => model(UserModel::class)
						->orderBy('created_at', 'DESC')
						->limit(5)
						->find(),
					'totalCourses' => $totalCourses,
					'activeCourses' => $activeCourses,
					'courses' => $courses,
					'teachers' => $userModelForTeachers->where('role', 'teacher')->findAll(),
				];
			} catch (\Exception $e) {
				log_message('error', 'Error loading admin dashboard data: ' . $e->getMessage());
				$adminData = [
					'totalUsers' => model(UserModel::class)->countAllResults(),
					'latestUsers' => model(UserModel::class)
						->orderBy('created_at', 'DESC')
						->limit(5)
						->find(),
					'totalCourses' => 0,
					'activeCourses' => 0,
					'courses' => [],
					'teachers' => [],
				];
			}
		}

		// Load role-specific data (teacher)
		if ($role === 'teacher') {
			$db = \Config\Database::connect();
			$courseModel = new CourseModel();
			$enrollmentModel = new \App\Models\EnrollmentModel();
			$userId = (int) $session->get('user_id');
			$totalCourses = $courseModel->where('instructor_id', $userId)->countAllResults();
			$totalStudents = (int) $db->query(
				"SELECT COUNT(DISTINCT e.user_id) AS cnt
				 FROM enrollments e
				 JOIN courses c ON c.id = e.course_id
				 WHERE c.instructor_id = ? AND e.status = 'approved'",
				[$userId]
			)->getRow('cnt');
			$recentEnrollments = $db->query(
				"SELECT u.name, u.email, c.title
				 FROM enrollments e
				 JOIN users u ON u.id = e.user_id
				 JOIN courses c ON c.id = e.course_id
				 WHERE c.instructor_id = ? AND e.status = 'approved'
				 ORDER BY e.enrollment_date DESC
				 LIMIT 5",
				[$userId]
			)->getResultArray();
			$pendingRequests = $enrollmentModel->getPendingRequestsForTeacher($userId);

			// Debug: Log pending requests
			log_message('info', 'Teacher ID: ' . $userId);
			log_message('info', 'Pending Requests Count: ' . count($pendingRequests));
			log_message('info', 'Pending Requests: ' . print_r($pendingRequests, true));

			$courses = $courseModel->where('instructor_id', $userId)->findAll();

			// Get all teachers for modal dropdown
			$userModelForTeachers = new UserModel();
			$allTeachers = $userModelForTeachers->where('role', 'teacher')->findAll();

			$teacherData = [
				'totalCourses' => $totalCourses,
				'totalStudents' => $totalStudents,
				'recentEnrollments' => $recentEnrollments,
				'pendingRequests' => $pendingRequests,
				'courses' => $courses,
				'teachers' => $allTeachers,
			];
		}

		// Load role-specific data (student)
		if ($role === 'student') {
			$db = \Config\Database::connect();
			$userId = (int) $session->get('user_id');
			$totalEnrolled = (int) $db->table('enrollments')->where('user_id', $userId)->countAllResults();
			$totalCompleted = 0; // Since we don't have status field anymore
			$myCourses = $db->query(
				"SELECT c.title, c.id, e.enrollment_date
				 FROM enrollments e
				 JOIN courses c ON c.id = e.course_id
				 WHERE e.user_id = ?
				 ORDER BY e.enrollment_date DESC
				 LIMIT 5",
				[$userId]
			)->getResultArray();
			$studentData = [
				'totalEnrolled' => $totalEnrolled,
				'totalCompleted' => $totalCompleted,
				'myCourses' => $myCourses,
			];
		}
		
		$data = [
			'user_name' => $session->get('user_name'),
			'user_email' => $session->get('user_email'),
			'role' => $role,
			'admin' => $adminData,
			'teacher' => $teacherData,
			'student' => $studentData,
		];
		
		return view('auth/dashboard', $data);
	}
}
