<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Tenant Role Presets Configuration
 *
 * กำหนดชุดสิทธิ์เริ่มต้นตาม Role Flag ของ Tenant
 */
class AuthTenantRoles extends BaseConfig
{
    /**
     * รายการ Role และ Permissions เริ่มต้น (module.action.scope)
     *
     * @var array<string, list<string>>
     */
    public array $roles = [
        'admin' => [
            '*.*.*',
        ],
        'manager' => [
            'people.*.*',
            'orders.*.*',
            'reports.view.*',
        ],
        'staff' => [
            'people.view.*',
            'orders.create.own',
            'orders.view.own',
        ],
        'member' => [
            'people.view.own',
        ],
    ];
}
