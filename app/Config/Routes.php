<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Home::index');
<<<<<<< HEAD
$routes->get('/home', 'Home::index'); 

$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

$routes->get('/', 'Auth::login');

$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');

$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');

$routes->get('/dashboard', 'Auth::dashboard');

$routes->get('/logout', 'Auth::logout');

=======

// Custom routes
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Auth & Dashboard
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attempt');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Home::dashboard');
// Registration
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::store');
>>>>>>> 4ce6d5449c1f03dd0a546ba78ef04f097ef7b778
