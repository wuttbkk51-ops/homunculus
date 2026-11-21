<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('admin', 'Admin\Dashboard::index');
$routes->get('dashboard', 'Admin\Dashboard::index');
$routes->get('profile', 'Admin\Profile::index');
$routes->get('settings', 'Admin\Settings::index');
$routes->get('starter', 'Home::starter');

// --- Invite Registration (ฝั่งผู้รับ, public route) ---
$routes->get('register/invite',  'Auth\InviteRegisterController::show');
$routes->post('register/invite', 'Auth\InviteRegisterController::store');

// --- Invite Management UI (ฝั่ง Admin, ผ่าน auth filter) ---
$routes->get('invitations',                  'Admin\Invitations::index');
$routes->get('invitations/create',           'Admin\Invitations::create');
$routes->post('invitations',                 'Admin\Invitations::store');
$routes->post('invitations/(:num)/revoke',   'Admin\Invitations::revoke/$1');
$routes->post('invitations/(:num)/resend',   'Admin\Invitations::resend/$1');

service('auth')->routes($routes);
