<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// Public routes (no auth required)
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// Authentication routes
$routes->group('auth', function($routes) {
    // Login routes
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::attemptLogin');
    $routes->get('logout', 'AuthController::logout');
    
    // Registration routes
    $routes->get('register', 'AuthController::register');
    $routes->post('register', 'AuthController::attemptRegister');
    
    // Password reset routes
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->post('forgot-password', 'AuthController::attemptForgotPassword');
    $routes->get('reset-password/(:segment)', 'AuthController::resetPassword/$1');
    $routes->post('reset-password', 'AuthController::attemptResetPassword');
    
    // Email verification routes
    $routes->get('verify-email/(:segment)', 'AuthController::verifyEmail/$1');
    $routes->get('resend-verification', 'AuthController::resendVerification');
});

// Public publication routes
$routes->get('publications', 'Publication::index');
$routes->get('publications/view/(:num)', 'Publication::view/$1');
$routes->get('publications/search', 'Publication::search');
$routes->post('publications/search', 'Publication::search');

// Protected routes (auth required)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Publication management routes
    $routes->get('publications/upload', 'Publication::upload');
    $routes->get('publications/create', 'Publication::create');
    $routes->post('publications/create', 'Publication::create');
    $routes->get('publications/edit/(:num)', 'Publication::edit/$1');
    $routes->post('publications/edit/(:num)', 'Publication::edit/$1');
    $routes->get('publications/delete/(:num)', 'Publication::delete/$1');
    $routes->get('publications/download/(:num)', 'Publication::download/$1');
    
    // Profile routes
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile/update', 'ProfileController::update');
    
    // Admin routes (admin role required)
    $routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
        $routes->get('', 'AdminController::index');
        $routes->get('manage-users', 'AdminController::manageUsers');
        $routes->get('manage-submissions', 'AdminController::manageSubmissions');
        $routes->get('view-analytics', 'AdminController::viewAnalytics');
    });
});

// API routes (auth required)
$routes->group('api', ['filter' => 'auth'], function($routes) {
    $routes->get('colleges/(:num)/departments', 'Api::getDepartments/$1');
    $routes->get('departments/(:num)/programs', 'Api::getPrograms/$1');
    $routes->get('publications/search', 'Api::searchPublications');
    $routes->get('publications/(:num)', 'Api::getPublication/$1');
    $routes->post('publications', 'Api::createPublication');
    $routes->put('publications/(:num)', 'Api::updatePublication/$1');
    $routes->delete('publications/(:num)', 'Api::deletePublication/$1');
});

// Test routes (only available in testing environment)
if (ENVIRONMENT === 'testing') {
    $routes->group('test', function($routes) {
        $routes->get('database', 'Test::database');
        $routes->get('controller', 'Test::controller');
        $routes->get('fileupload', 'Test::fileupload');
        $routes->get('search', 'Test::search');
        $routes->get('api', 'Test::api');
        $routes->get('view', 'Test::view');
        $routes->get('javascript', 'Test::javascript');
        $routes->get('security', 'Test::security');
        $routes->get('performance', 'Test::performance');
        $routes->get('browser', 'Test::browser');
        $routes->get('userexperience', 'Test::userexperience');
    });
}

// Debug route (temporary)
$routes->get('debug/session', function() {
    echo '<pre>';
    print_r(session()->get());
    echo '</pre>';
    die();
});