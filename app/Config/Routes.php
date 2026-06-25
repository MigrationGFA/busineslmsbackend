<?php

// namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


// CORS Preflight Handler
$routes->options('(:any)', static function () {
    return service('response')->setStatusCode(200);
});


// Default route
$routes->get('/', 'Home::index');

// Add inside app/Config/Routes.php

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {

    // Landing page: GET /api/apply/ogun -> finds the currently open cohort for Ogun
    $routes->get('apply/(:segment)', 'CohortController::showByState/$1');

    // Paid registration form
    $routes->post('apply/(:segment)/register', 'UserController::store/$1');

    // Brochure email capture
    $routes->post('apply/(:segment)/brochure', 'BrochureController::send/$1');

    // Page view / click tracking
    $routes->post('events/page-view', 'PageViewEventController::store');

});

