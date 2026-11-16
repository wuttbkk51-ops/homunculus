<?php

namespace App\Filters;

use App\Services\BranchService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter สำหรับตรวจสอบว่าผู้ใช้ได้เลือก Active Branch ใน Session แล้วหรือยัง (Layer 1 Defense)
 */
class ActiveBranchFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! auth()->loggedIn()) {
            return null;
        }

        $user = auth()->user();

        // SuperAdmin ยกเว้นการบังคับเลือกสาขา
        if ($user && $user->isSuperAdmin()) {
            return null;
        }

        /** @var BranchService $branchService */
        $branchService = service('branch_service');
        $activeBranchId = $branchService->getActiveBranchId();

        if ($activeBranchId === null) {
            return redirect()->to('auth/select-branch');
        }

        // ตรวจสอบว่าผู้ใช้ยังมีสิทธิ์ในสาขานี้จริงๆ
        if (! $branchService->hasBranchAccess((int) $user->id, $activeBranchId)) {
            $branchService->clearActiveBranch();

            return redirect()->to('auth/select-branch')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงสาขาดังกล่าว');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }
}
