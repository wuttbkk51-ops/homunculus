<?php

namespace App\Filters;

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

        $activeBranch = session()->get('active_branch');

        if (empty($activeBranch['id'])) {
            /** @var \App\Services\BranchService $branchService */
            $branchService = service('branch_service');
            $activeBranch  = $branchService->autoSelectDefaultBranch((int) $user->id);

            if ($activeBranch === null) {
                auth()->logout();

                return redirect()->to('login')->with('error', 'คุณยังไม่ได้รับมอบหมายสาขาในการทำงาน');
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }
}
