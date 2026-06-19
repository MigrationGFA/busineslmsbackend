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

$routes->group('api', function ($routes) {

    // Landing page reads cohort branding/price by slug
    $routes->get('cohorts/(:segment)', 'Api\CohortController::show/$1');

    // Paid registration form
    $routes->post('cohorts/(:segment)/register', 'Api\RegistrationController::store/$1');

    // Brochure email capture
    $routes->post('cohorts/(:segment)/brochure', 'Api\BrochureController::send/$1');

});


