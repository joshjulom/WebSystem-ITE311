<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// Authentication Routes
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');

// Debug routes for testing

// Course routes
$routes->get('/courses', 'Course::index');
$routes->get('/course', 'Course::index');
$routes->get('/my-courses', 'Course::myCourses');
$routes->get('/course/search', 'Course::search');
$routes->post('/course/search', 'Course::search');

// Course enrollment route
$routes->post('/course/enroll', 'Course::enroll');
$routes->post('/course/approve-enrollment/(:num)', 'Course::approveEnrollment/$1');
$routes->post('/course/reject-enrollment/(:num)', 'Course::rejectEnrollment/$1');
$routes->post('/course/remove-rejected-enrollment/(:num)', 'Course::removeRejectedEnrollment/$1');

// Course management routes (admin/teacher only)
$routes->get('/course/show/(:num)', 'Course::show/$1');
$routes->get('/course/create', 'Course::create');
$routes->post('/course/store', 'Course::store');
$routes->get('/course/edit/(:num)', 'Course::edit/$1');
$routes->post('/course/update/(:num)', 'Course::update/$1');
$routes->post('/course/delete/(:num)', 'Course::delete/$1');
$routes->get('/course/assign-teacher/(:num)', 'Course::assignTeacher/$1');
$routes->post('/course/assign-teacher/(:num)', 'Course::assignTeacher/$1');
$routes->get('/course/manage-students/(:num)', 'Course::manageStudents/$1');
$routes->post('/course/update-teacher/(:num)', 'Course::updateTeacher/$1');
$routes->post('/course/remove-student/(:num)', 'Course::removeStudent/$1');

// Materials Routes
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');

// Notifications Routes
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');
$routes->get('/notifications/all', 'Notifications::all');

// Admin Routes
$routes->get('/admin/dashboard', 'Admin::dashboard');
$routes->get('/admin/users', 'Admin::users');
$routes->post('/admin/updateRole', 'Admin::updateRole');
$routes->post('/admin/addUser', 'Admin::addUser');
$routes->post('/admin/updateUser/(:num)', 'Admin::updateUser/$1');
$routes->post('/admin/changePassword/(:num)', 'Admin::changePassword/$1');
$routes->post('/admin/toggleStatus/(:num)', 'Admin::toggleStatus/$1');
$routes->post('/admin/deleteUser/(:num)', 'Admin::deleteUser/$1');

// Admin Course Management Routes
$routes->get('/admin/course/search', 'Admin::courseSearch');
$routes->post('/admin/course/updateStatus/(:num)', 'Admin::updateCourseStatus/$1');
$routes->post('/admin/course/updateDetails/(:num)', 'Admin::updateCourseDetails/$1');

// Assignment Routes
// Teacher Routes
$routes->get('/assignment/teacher-view/(:num)', 'Assignment::teacherView/$1');
$routes->get('/assignment/create/(:num)', 'Assignment::create/$1');
$routes->post('/assignment/store', 'Assignment::store');
$routes->get('/assignment/view-submissions/(:num)', 'Assignment::viewSubmissions/$1');
$routes->post('/assignment/grade', 'Assignment::grade');
$routes->post('/assignment/delete/(:num)', 'Assignment::delete/$1');

// Student Routes
$routes->get('/assignment/student-view/(:num)', 'Assignment::studentView/$1');
$routes->get('/assignment/submit-form/(:num)', 'Assignment::submitForm/$1');
$routes->post('/assignment/submit', 'Assignment::submit');

// Download Routes (both teacher and student)
$routes->get('/assignment/download-assignment/(:num)', 'Assignment::downloadAssignment/$1');
$routes->get('/assignment/download-submission/(:num)', 'Assignment::downloadSubmission/$1');

// Announcement Routes
$routes->get('/announcements', 'Announcement::index');
$routes->get('/announcement/manage', 'Announcement::manage');
$routes->get('/announcement/create', 'Announcement::create');
$routes->post('/announcement/store', 'Announcement::store');
$routes->get('/announcement/edit/(:num)', 'Announcement::edit/$1');
$routes->post('/announcement/update/(:num)', 'Announcement::update/$1');
$routes->post('/announcement/toggle-status/(:num)', 'Announcement::toggleStatus/$1');
$routes->post('/announcement/delete/(:num)', 'Announcement::delete/$1');
