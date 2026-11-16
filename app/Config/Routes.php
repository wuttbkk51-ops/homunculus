<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Shield Authentication Routes (login, register, logout, etc.)
service('auth')->routes($routes);

// Contextual Authentication Routes (Switch Branch) - Guarded by Shield session
$routes->group('', ['filter' => 'session'], static function (RouteCollection $routes): void {
    $routes->get('switch-branch/(:num)', 'AuthController::switchBranch/$1');
    $routes->post('switch-branch/(:num)', 'AuthController::switchBranch/$1');
});
$routes->get('auth/tenant-suspended', 'AuthController::tenantSuspendedView');

// Clean Admin Routes (Guarded by Layer 1 Filters: session, tenant_active, active_branch)
$routes->group('', [
    'namespace' => 'App\Controllers\Admin',
    'filter'    => ['session', 'tenant_active', 'active_branch'],
], static function (RouteCollection $routes): void {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('admin', 'DashboardController::index');
    // เพิ่ม routes ของ admin เพิ่มเติมที่นี่ (Clean URLs เช่น /products, /sales, /users)
});
