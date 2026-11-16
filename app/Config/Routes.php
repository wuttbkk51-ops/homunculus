<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Shield Authentication Routes (login, register, logout, etc.)
service('auth')->routes($routes);

// Contextual Authentication Routes (Select Branch, Switch Branch, Tenant Suspended)
$routes->get('auth/select-branch', 'AuthController::selectBranchView');
$routes->post('auth/select-branch', 'AuthController::selectBranchSubmit');
$routes->get('auth/switch-branch/(:num)', 'AuthController::switchBranch/$1');
$routes->get('auth/tenant-suspended', 'AuthController::tenantSuspendedView');

// Admin Routes (Guarded by Layer 1 Filters: session, tenant_active, active_branch)
$routes->group('admin', static function (RouteCollection $routes): void {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');
});
