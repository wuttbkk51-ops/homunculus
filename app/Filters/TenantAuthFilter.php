<?php

declare(strict_types=1);

namespace App\Filters;

use App\Models\TenantModel;
use App\Services\TenantAuthService;
use App\Services\TenantService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter ตรวจสอบการยืนยันตัวตน, กำหนดบริบท Tenant ผ่าน UUID, ตรวจสอบ Cache Flag และแจ้งเตือน Client เมื่อสิทธิ์เปลี่ยน
 */
class TenantAuthFilter implements FilterInterface
{
    protected static bool $authRefreshed = false;

    public function before(RequestInterface $request, $arguments = null)
    {
        static::$authRefreshed = false;

        if (! $request instanceof IncomingRequest) {
            return $request;
        }

        if (! auth()->loggedIn()) {
            return $this->fail(401, 'loginRequired');
        }

        $user = auth()->user();
        $uuid = $request->getHeaderLine('X-Tenant-ID');
        if ($uuid === '') {
            $uuid = (string) session('active_tenant_uuid', '');
        }

        // Admin ทำงานระดับ Platform เมื่อไม่ได้ระบุ Tenant
        if ($user->inGroup('admin') && $uuid === '') {
            TenantModel::setCurrentTenantId(0);

            return $request;
        }

        if ($uuid === '') {
            return $this->fail(400, 'tenantRequired');
        }

        $tenantService = new TenantService();
        $tenant        = $tenantService->findByUuid($uuid);

        if (! $tenant || $tenant['status'] !== 'active') {
            return $this->fail(404, 'tenantNotFound');
        }

        $tenantId = (int) $tenant['id'];
        TenantModel::setCurrentTenantId($tenantId);

        if ($user->inGroup('admin')) {
            return $request;
        }

        $userId      = (int) $user->id;
        $authService = new TenantAuthService();

        if ($authService->getUserAuthCache($tenantId, $userId) !== null) {
            return $request;
        }

        $member = $tenantService->getMember($tenantId, $userId);

        // 404 เหมือนกรณีไม่พบ Tenant เพื่อไม่เปิดเผยว่า Tenant มีอยู่จริง
        if (! $member || $member['status'] !== 'active') {
            return $this->fail(404, 'tenantNotFound');
        }

        $authService->setUserAuthCache($tenantId, $userId, [
            'role'         => $member['role'],
            'permissions'  => $authService->resolveUserPermissions($tenantId, $userId, $member['role']),
            'refreshed_at' => date('Y-m-d H:i:s'),
        ]);

        static::$authRefreshed = true;

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (static::$authRefreshed) {
            $response->setHeader('X-Auth-Refreshed', 'true');
        }

        return $response;
    }

    protected function fail(int $status, string $key): ResponseInterface
    {
        return service('response')
            ->setStatusCode($status)
            ->setJSON([
                'status'  => 'error',
                'code'    => $key,
                'message' => lang("TenantAuth.{$key}"),
            ]);
    }
}
