<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('admin', 'Admin\Dashboard::index');
$routes->get('dashboard', 'Admin\Dashboard::index');
$routes->get('profile', 'Admin\Profile::index');
$routes->get('settings', 'Admin\Settings::index');

service('auth')->routes($routes);
