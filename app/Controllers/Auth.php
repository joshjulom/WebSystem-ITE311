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
				'name' => 'required|min_length[3]|max_length[100]',
				'email' => 'required|valid_email|is_unique[users.email]',
				'password' => 'required|min_length[6]',
				'password_confirm' => 'matches[password]'
			];
			
			if ($this->validate($rules)) {
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
				$loginLogModel = new LoginLogModel();
				$adminData = [
					'totalUsers' => model(UserModel::class)->countAllResults(),
					'latestUsers' => model(UserModel::class)
						->orderBy('created_at', 'DESC')
						->limit(5)
						->find(),
					'recentLogins' => $loginLogModel->getRecentLogins(10),
					'loginStatsByRole' => $loginLogModel->getLoginStatsByRole(7),
					'recentUniqueUsers' => $loginLogModel->getRecentUniqueUsers(7, 10),
				];
			} catch (\Exception $e) {
				log_message('error', 'Error loading admin dashboard data: ' . $e->getMessage());
				$adminData = [
					'totalUsers' => model(UserModel::class)->countAllResults(),
					'latestUsers' => model(UserModel::class)
						->orderBy('created_at', 'DESC')
						->limit(5)
						->find(),
					'recentLogins' => [],
					'loginStatsByRole' => [],
					'recentUniqueUsers' => [],
				];
			}
		}

		// Load role-specific data (teacher)
		if ($role === 'teacher') {
			$db = \Config\Database::connect();
			$courseModel = new CourseModel();
			$userId = (int) $session->get('user_id');
			$totalCourses = $courseModel->where('instructor_id', $userId)->countAllResults();
			$totalStudents = (int) $db->query(
				"SELECT COUNT(DISTINCT e.user_id) AS cnt
				 FROM enrollments e
				 JOIN courses c ON c.id = e.course_id
				 WHERE c.instructor_id = ?",
				[$userId]
			)->getRow('cnt');
			$recentEnrollments = $db->query(
				"SELECT u.name, u.email, c.title
				 FROM enrollments e
				 JOIN users u ON u.id = e.user_id
				 JOIN courses c ON c.id = e.course_id
				 WHERE c.instructor_id = ?
				 ORDER BY e.enrollment_date DESC
				 LIMIT 5",
				[$userId]
			)->getResultArray();
    $courses = $courseModel->where('instructor_id', $userId)->findAll();
    $teacherData = [
				'totalCourses' => $totalCourses,
				'totalStudents' => $totalStudents,
				'recentEnrollments' => $recentEnrollments,
				'courses' => $courses,
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
