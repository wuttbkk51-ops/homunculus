<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Dashboard::index');
$routes->get('dashboard', 'Dashboard::index');
$routes->get('profile', 'Profile::index');
$routes->get('settings', 'Settings::index');
$routes->get('starter', 'Home::starter');

service('auth')->routes($routes);
