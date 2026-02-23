<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'ItemsController::index');           // Fetch all items


//------------ Lost & Found API Routes ------------------
$routes->get('/items', 'ItemsController::index');          // Fetch all items
$routes->post('/items/insert', 'ItemsController::insert'); // Insert new item
$routes->post('items/update', 'ItemsController::update');  // Update by POST only
$routes->post('/items/delete', 'ItemsController::delete'); // Delete by POST only

//-------------- User log Table Routes --------------------
$routes->post('/auth/register', 'Auth::register');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');

// ----------------- ADMIN USER MANAGEMENT -----------------


$routes->group('api', function($routes) {
    $routes->get('getUsers', 'UserController::getUsers');
    $routes->post('updateUserStatus', 'UserController::updateStatus');
    $routes->get('deleteUser/(:num)', 'UserController::deleteUser/$1');
});


// ----------------- Lost & Found Admin Routes ------------------

$routes->get('create', 'Admin_InsertController::create');    // Show insert form
$routes->post('store', 'Admin_InsertController::store');      // Save data to DB

$routes->get('/', 'Admin_AuthController::doLogin');
$routes->post('admin/doLogin', 'Admin_AuthController::doLogin');
$routes->get('admin/logout', 'Admin_AuthController::logout');



