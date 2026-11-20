<?php

declare(strict_types=1);

namespace App\Filters;

use App\Models\TenantModel;
use App\Services\AuthService;
use App\Services\TenantService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter ตรวจสอบการยืนยันตัวตน, กำหนดบริบท Tenant ผ่าน UUID, ตรวจสอบ Cache Flag และแจ้งเตือน Client เมื่อสิทธิ์เปลี่ยน
 */
class AuthFilter implements FilterInterface
{
    // Path สำหรับ Redirect ในฝั่ง Web เมื่อไม่ผ่านการตรวจสอบ
    protected string $loginPath = '/login';
    protected string $selectTenantPath = '/select-tenant';

    // Flag ส่งต่อสถานะจาก before() ไป after() เพื่อแนบ Header X-Auth-Refreshed เมื่อสิทธิ์ถูกคำนวณใหม่
    protected static bool $authRefreshed = false;

    // ประเภท Client ของ Request ปัจจุบัน (ตั้งค่าครั้งเดียวใน before())
    protected bool $isApi = false;

    public function __construct(
        protected TenantService $tenantService = new TenantService(),
        protected AuthService $authService = new AuthService(),
    ) {}

    public function before(RequestInterface $request, $arguments = null)
    {
        static::$authRefreshed = false;

        if (! $request instanceof IncomingRequest) {
            return $request;
        }

        $this->isApi = $this->isApiRequest($request);

        if (! auth()->loggedIn()) {
            return $this->deny(401, 'loginRequired', $this->loginPath);
        }

        $user    = auth()->user();
        $isAdmin = $user->inGroup('admin');

        // API ใช้ Header อย่างเดียว / Web ใช้ Session ก่อน แล้ว fallback เป็น Header (สลับ Tenant)
        $header = $request->getHeaderLine('X-Tenant-ID');
        $uuid   = $this->isApi ? $header : ((string) session('active_tenant_uuid') ?: $header);

        if ($uuid === '') {
            if (! $isAdmin) {
                return $this->deny(400, 'tenantRequired', $this->selectTenantPath, 'warning');
            }

            // Admin ทำงานระดับ Platform เมื่อไม่ได้ระบุ Tenant
            TenantModel::setCurrentTenantId(0);

            return $request;
        }

        $tenant = $this->tenantService->findByUuid($uuid);

        if (! $tenant || $tenant['status'] !== 'active') {
            return $this->notFound();
        }

        $tenantId = (int) $tenant['id'];
        TenantModel::setCurrentTenantId($tenantId);

        if (! $this->isApi) {
            session()->set('active_tenant_uuid', $uuid);
        }

        $userId = (int) $user->id;

        if ($isAdmin || $this->authService->getUserAuthCache($tenantId, $userId) !== null) {
            return $request;
        }

        $member = $this->tenantService->getMember($tenantId, $userId);

        // 404 เหมือนกรณีไม่พบ Tenant เพื่อไม่เปิดเผยว่า Tenant มีอยู่จริง
        if (! $member || $member['status'] !== 'active') {
            return $this->notFound();
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
        return static::$authRefreshed
            ? $response->setHeader('X-Auth-Refreshed', 'true')
            : $response;
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
     * ไม่พบ Tenant หรือสมาชิกภาพไม่ถูกต้อง — ล้าง Session ป้องกัน Loop
     */
    protected function notFound(): ResponseInterface
    {
        if (! $this->isApi) {
            session()->remove('active_tenant_uuid');
        }

        return $this->deny(404, 'tenantNotFound', $this->selectTenantPath);
    }

    /**
     * ตอบกลับ Error ตามประเภท Client: JSON สำหรับ API, Redirect + Flash สำหรับ Web
     */
    protected function deny(int $status, string $key, string $redirectTo, string $flash = 'error'): ResponseInterface
    {
        $message = lang("TenantAuth.{$key}");

        return $this->isApi
            ? service('response')->setStatusCode($status)->setJSON([
                'status'  => 'error',
                'code'    => $key,
                'message' => $message,
            ])
            : redirect()->to($redirectTo)->with($flash, $message);
    }
}