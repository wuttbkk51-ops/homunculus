<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InvitationModel;
use App\Services\InvitationService;
use CodeIgniter\HTTP\RedirectResponse;
use RuntimeException;

/**
 * Controller สำหรับ Admin จัดการ Invitation
 *
 * หมายเหตุ: ยังไม่บังคับ Permission Guard ในขั้นนี้
 * (จะกำหนดผ่านระบบ permission ในขั้นตอนออกแบบต่อไป)
 *
 * GET  /invitations                 → รายการ Invitation ทั้งหมด
 * GET  /invitations/create          → ฟอร์มสร้าง Invitation
 * POST /invitations                 → บันทึก Invitation ใหม่
 * POST /invitations/{id}/revoke     → ยกเลิก Invitation
 * POST /invitations/{id}/resend     → ส่ง Invitation ซ้ำ (Token ใหม่)
 */
class Invitations extends BaseController
{
    public function __construct(
        protected InvitationService $inviteService = new InvitationService(),
        protected InvitationModel   $model         = new InvitationModel(),
    ) {}

    public function index(): string
    {
        $invitations = $this->model->findAllWithTenant();

        // คำนวณสถานะของแต่ละ Invitation ในระดับ View
        $now = date('Y-m-d H:i:s');
        foreach ($invitations as &$inv) {
            if ($inv['accepted_at'] !== null) {
                $inv['status'] = 'accepted';
            } elseif ($inv['expires_at'] !== null && $inv['expires_at'] <= $now) {
                $inv['status'] = 'expired';
            } else {
                $inv['status'] = 'pending';
            }
        }
        unset($inv);

        return view('invitations/index', [
            'title'       => lang('Invite.pageTitle'),
            'invitations' => $invitations,
        ]);
    }

    public function create(): string
    {
        return view('invitations/create', [
            'title' => lang('Invite.sendInvite'),
        ]);
    }

    public function store(): RedirectResponse
    {
        $rules = [
            'email'      => 'required|valid_email|max_length[254]',
            'role_group' => 'required|max_length[50]',
            'invite_type'=> 'required|in_list[new_tenant,existing_tenant]',
        ];

        $post = $this->request->getPost();

        if ($post['invite_type'] === 'existing_tenant') {
            $rules['tenant_id'] = 'required|is_natural_no_zero';
        }

        if (! $this->validateData($post, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data     = $this->validator->getValidated();
        $isNewTenant = $data['invite_type'] === 'new_tenant';
        $tenantId = $isNewTenant ? null : (int) $data['tenant_id'];

        // Hybrid: Admin กำหนด tenant_name/slug ล่วงหน้าได้ ถ้าว่างจะให้ผู้รับกรอกเอง
        $tenantName = ! empty($post['tenant_name']) ? trim($post['tenant_name']) : null;
        $tenantSlug = ! empty($post['tenant_slug']) ? trim($post['tenant_slug']) : null;

        try {
            $invitedBy = auth()->id();
            $rawToken  = $this->inviteService->createInvite(
                email:      $data['email'],
                roleGroup:  $data['role_group'],
                tenantId:   $tenantId,
                invitedBy:  $invitedBy,
                tenantName: $tenantName,
                tenantSlug: $tenantSlug,
            );

            // ในช่วง dev ให้ log Raw Token แทนการส่งอีเมลจริง
            $inviteLink = site_url("register/invite?token={$rawToken}");
            log_message('info', "[Invite] ส่งคำเชิญไปยัง {$data['email']} | link: {$inviteLink}");

            // TODO: แทนที่ log_message ด้วยการส่งอีเมลจริงเมื่อระบบ Email พร้อม
            // $this->sendInviteEmail($data['email'], $inviteLink);

            return redirect()->to('/invitations')->with('message', lang('Invite.sendSuccess'));

        } catch (RuntimeException $e) {
            log_message('error', '[Invitations::store] ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', lang('Invite.createFailed'));
        }
    }

    public function revoke(int $id): RedirectResponse
    {
        $this->inviteService->revokeInvite($id);

        return redirect()->to('/invitations')->with('message', lang('Invite.revokeSuccess'));
    }

    public function resend(int $id): RedirectResponse
    {
        try {
            $rawToken   = $this->inviteService->resendInvite($id);
            $invitation = $this->model->find($id);
            $inviteLink = site_url("register/invite?token={$rawToken}");

            log_message('info', "[Invite] Resend ไปยัง {$invitation['email']} | link: {$inviteLink}");

            // TODO: แทนที่ด้วยการส่งอีเมลจริง

            return redirect()->to('/invitations')->with('message', lang('Invite.resendSuccess'));

        } catch (\Throwable $e) {
            log_message('error', '[Invitations::resend] ' . $e->getMessage());

            return redirect()->back()->with('error', lang('Invite.createFailed'));
        }
    }
}
