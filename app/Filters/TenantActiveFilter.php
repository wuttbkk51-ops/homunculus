<?php

namespace App\Filters;

use App\Services\TenantService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter สำหรับตรวจสอบสถานะของ Tenant ว่ายัง Active อยู่หรือไม่ (Layer 1 Defense)
 * ทำงานผ่าน TenantService Caching เพื่อไม่ให้เกิด DB Query ซ้ำซ้อน
 */
class TenantActiveFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! auth()->loggedIn()) {
            return null;
        }

        $user = auth()->user();

        // SuperAdmin ยกเว้นการตรวจสอบ Tenant
        if ($user && $user->isSuperAdmin()) {
            return null;
        }

        $tenantId = $user ? $user->getTenantId() : null;
        if ($tenantId === null) {
            auth()->logout();

            return redirect()->to('login')->with('error', 'ไม่พบข้อมูลองค์กร (Tenant) ของบัญชีนี้');
        }

        /** @var TenantService $tenantService */
        $tenantService = service('tenant_service');

        if (! $tenantService->isTenantActive($tenantId)) {
            auth()->logout();

            return redirect()->to('auth/tenant-suspended');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }
}
