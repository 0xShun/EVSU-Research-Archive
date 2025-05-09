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

// Home routes
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// Publication routes
$routes->get('publications', 'Publication::index');
$routes->get('publications/upload', 'Publication::upload');
$routes->get('publications/create', 'Publication::create');
$routes->post('publications/create', 'Publication::create');
$routes->get('publications/view/(:num)', 'Publication::view/$1');
$routes->get('publications/edit/(:num)', 'Publication::edit/$1');
$routes->post('publications/edit/(:num)', 'Publication::edit/$1');
$routes->get('publications/delete/(:num)', 'Publication::delete/$1');
$routes->get('publications/download/(:num)', 'Publication::download/$1');
$routes->get('publications/search', 'Publication::search');
$routes->post('publications/search', 'Publication::search');

// API routes
$routes->group('api', function($routes) {
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

// User routes
$routes->get('user/register', 'UserController::register');
$routes->post('user/register', 'UserController::register');
$routes->get('user/login', 'UserController::login');
$routes->post('user/login', 'UserController::login');
$routes->get('user/logout', 'UserController::logout');

// Admin routes
$routes->get('admin', 'AdminController::index');
$routes->get('admin/manage-users', 'AdminController::manageUsers');
$routes->get('admin/manage-submissions', 'AdminController::manageSubmissions');
$routes->get('admin/view-analytics', 'AdminController::viewAnalytics');
