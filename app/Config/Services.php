<?php

namespace Config;

use App\Services\AuthService;
use App\Services\BranchService;
use App\Services\TenantService;
use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 */
class Services extends BaseService
{
    public static function tenant_service(bool $getShared = true): TenantService
    {
        if ($getShared) {
            return static::getSharedInstance('tenant_service');
        }

        return new TenantService();
    }

    public static function branch_service(bool $getShared = true): BranchService
    {
        if ($getShared) {
            return static::getSharedInstance('branch_service');
        }

        return new BranchService();
    }

    public static function auth_service(bool $getShared = true): AuthService
    {
        if ($getShared) {
            return static::getSharedInstance('auth_service');
        }

        return new AuthService(
            static::tenant_service(),
            static::branch_service(),
            config('AuthGroups')
        );
    }
}
