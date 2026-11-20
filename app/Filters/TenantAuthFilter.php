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

    public function __construct(
        protected TenantService $tenantService = new TenantService(),
        protected TenantAuthService $authService = new TenantAuthService(),
    ) {}

    public function before(RequestInterface $request, $arguments = null)
    {
        static::$authRefreshed = false;

        if (! $request instanceof IncomingRequest) {
            return $request;
        }

        $isApi = $this->isApiRequest($request);

        if (! auth()->loggedIn()) {
            return $this->fail(401, 'loginRequired', $isApi);
        }

        $user = auth()->user();

        // ดึง Tenant UUID ตามประเภท Client
        $uuid = $isApi
            ? $request->getHeaderLine('X-Tenant-ID')
            : (string) session('active_tenant_uuid', '');

        // กรณี Web Client มีการส่ง Header สลับ Tenant ให้ยอมรับได้
        if (! $isApi && $uuid === '' && $request->hasHeader('X-Tenant-ID')) {
            $uuid = $request->getHeaderLine('X-Tenant-ID');
        }

        // Admin ทำงานระดับ Platform เมื่อไม่ได้ระบุ Tenant
        if ($user->inGroup('admin') && $uuid === '') {
            TenantModel::setCurrentTenantId(0);

            return $request;
        }

        if ($uuid === '') {
            return $this->fail(400, 'tenantRequired', $isApi);
        }

        $tenant = $this->tenantService->findByUuid($uuid);

        if (! $tenant || $tenant['status'] !== 'active') {
            if (! $isApi) {
                session()->remove('active_tenant_uuid');
            }

            return $this->fail(404, 'tenantNotFound', $isApi);
        }

        $tenantId = (int) $tenant['id'];
        TenantModel::setCurrentTenantId($tenantId);

        // หากเป็น Web Client และ Tenant ถูกต้อง ให้ sync เก็บไว้ใน Session
        if (! $isApi && session('active_tenant_uuid') !== $uuid) {
            session()->set('active_tenant_uuid', $uuid);
        }

        if ($user->inGroup('admin')) {
            return $request;
        }

        $userId = (int) $user->id;

        if ($this->authService->getUserAuthCache($tenantId, $userId) !== null) {
            return $request;
        }

        $member = $this->tenantService->getMember($tenantId, $userId);

        // 404 เหมือนกรณีไม่พบ Tenant เพื่อไม่เปิดเผยว่า Tenant มีอยู่จริง
        if (! $member || $member['status'] !== 'active') {
            if (! $isApi) {
                session()->remove('active_tenant_uuid');
            }

            return $this->fail(404, 'tenantNotFound', $isApi);
        }

        $this->authService->setUserAuthCache($tenantId, $userId, [
            'role'         => $member['role'],
            'permissions'  => $this->authService->resolveUserPermissions($tenantId, $userId, $member['role']),
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

    /**
     * ตรวจสอบว่า Request มาจาก API, AJAX หรือ Client ที่ต้องการ JSON หรือไม่
     */
    protected function isApiRequest(IncomingRequest $request): bool
    {
        return $request->isAJAX()
            || str_starts_with(trim($request->getUri()->getPath(), '/'), 'api')
            || str_contains($request->getHeaderLine('Accept'), 'application/json')
            || $request->hasHeader('Authorization');
    }

    /**
     * ส่ง Response ข้อผิดพลาดตามประเภท Client
     */
    protected function fail(int $status, string $key, bool $isApi): ResponseInterface
    {
        $message = lang("TenantAuth.{$key}");

        if ($isApi) {
            return service('response')
                ->setStatusCode($status)
                ->setJSON([
                    'status'  => 'error',
                    'code'    => $key,
                    'message' => $message,
                ]);
        }

        return match ($key) {
            'loginRequired'  => redirect()->to('/login')->with('error', $message),
            'tenantRequired' => redirect()->to('/select-tenant')->with('warning', $message),
            default          => redirect()->to('/select-tenant')->with('error', $message),
        };
    }
}
