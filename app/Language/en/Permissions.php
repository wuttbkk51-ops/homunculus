<?php

declare(strict_types=1);

/**
 * English language translations for permissions and modules
 * Used with navbar_helper and contextual RBAC
 */
return [
    // --------------------------------------------------------------------------
    // Module-Level Names & Aliases
    // --------------------------------------------------------------------------
    'dashboard'  => 'Dashboard',
    'pos'        => 'Point of Sale (POS)',
    'sales'      => 'Sales & POS',
    'sale'       => 'Sales & POS',
    'items'      => 'Items & Products',
    'item'       => 'Items & Products',
    'products'   => 'Products & Services',
    'product'    => 'Products & Services',
    'categories' => 'Categories',
    'category'   => 'Categories',
    'inventory'  => 'Inventory & Stock',
    'customers'  => 'Customers & Members',
    'customer'   => 'Customers & Members',
    'users'      => 'Users & Staff',
    'user'       => 'Users & Staff',
    'branches'   => 'Branches',
    'branch'     => 'Branches',
    'reports'    => 'Reports & Analytics',
    'report'     => 'Reports & Analytics',
    'settings'   => 'Settings',
    'setting'    => 'Settings',
    'tenants'    => 'Tenants & Organizations',
    'tenant'     => 'Tenants & Organizations',

    // --------------------------------------------------------------------------
    // Granular Action & Scope Keys
    // --------------------------------------------------------------------------
    // Platform / Tenant Level
    'tenant.manage.all'         => 'Manage and Suspend Tenants',

    // Branch Module
    'branch.view.all'           => 'View All Branches',
    'branch.manage.all'         => 'Manage Branches',

    // User Module
    'user.view.all'             => 'View All Users',
    'user.manage.all'           => 'Manage Users and Branch Assignments',

    // Product Module
    'product.view.all'          => 'View Products and Pricing',
    'product.create.all'        => 'Add New Product',
    'product.edit.all'          => 'Edit Product and Pricing',
    'product.delete.all'        => 'Delete Product',

    // Inventory Module
    'inventory.view.branch'     => 'View Branch Stock Levels',
    'inventory.view.all'        => 'View All Branches Stock Levels',
    'inventory.adjust.branch'   => 'Adjust and Count Stock',
    'inventory.transfer.branch' => 'Transfer Stock Between Branches',

    // Sale & POS Module
    'sale.view.own'             => 'View Own Sales History',
    'sale.view.branch'          => 'View Branch Sales History',
    'sale.view.all'             => 'View All Branches Sales',
    'sale.create.branch'        => 'Create Sale Order (POS)',
    'sale.void.branch'          => 'Void Sale and Refund',
    'sale.delete.branch'        => 'Delete Sale Record',

    // Customer Module
    'customer.view.all'         => 'View Customer List',
    'customer.create.all'       => 'Register New Customer',
    'customer.edit.all'         => 'Edit Customer and Loyalty Points',

    // Report Module
    'report.view.branch'        => 'View Branch Sales Reports',
    'report.view.all'           => 'View Overall Sales Reports',
    'report.export.all'         => 'Export Reports (Excel/PDF)',

    // System Settings & Admin Access
    'admin.access'              => 'Access Administration Panel',
    'admin.settings'            => 'Manage Admin Settings',
];
