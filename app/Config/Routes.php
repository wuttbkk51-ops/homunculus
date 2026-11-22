<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Guest routes (Public)
$routes->group('', ['namespace' => 'App\Controllers\Guest'], static function ($routes) {
    $routes->get('about', 'About::index'); // domain.com/about
});

// Admin routes (Protected)
$routes->group('', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('admin', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');
});

service('auth')->routes($routes);
