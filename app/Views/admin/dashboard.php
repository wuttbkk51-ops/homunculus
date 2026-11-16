<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการ - Homunculus</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; min-height: 100vh; }
        header { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 1rem 2rem; display: flex; align-items: center; justify-content: space-between; }
        .logo { font-size: 1.25rem; font-weight: 700; color: #2563eb; }
        .user-nav { display: flex; align-items: center; gap: 1rem; }
        .active-branch-pill { background: #eff6ff; color: #1d4ed8; font-size: 0.8125rem; font-weight: 600; padding: 0.35rem 0.75rem; border-radius: 9999px; border: 1px solid #bfdbfe; }
        .container { max-width: 64rem; margin: 2rem auto; padding: 0 1rem; }
        .card { background: #ffffff; border-radius: 0.5rem; border: 1px solid #e2e8f0; padding: 1.5rem; margin-bottom: 1.5rem; }
        h2 { font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        th, td { text-align: left; padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; }
        th { font-weight: 600; color: #64748b; background-color: #f8fafc; }
        .badge-allowed { background: #dcfce7; color: #15803d; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 0.25rem; display: inline-block; }
        .badge-denied { background: #fee2e2; color: #b91c1c; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 0.25rem; display: inline-block; }
        .btn-link { color: #2563eb; text-decoration: none; font-size: 0.875rem; font-weight: 500; }
        .btn-link:hover { text-decoration: underline; }
        .btn-logout { background: none; border: none; color: #dc2626; cursor: pointer; font-size: 0.875rem; font-weight: 600; }
        .btn-logout:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <div class="logo">Homunculus Admin</div>
        <div class="user-nav">
            <?php if (! empty($activeBranch)): ?>
                <span class="active-branch-pill">
                    สาขา: <?= esc($activeBranch['name']) ?> (<?= esc($activeBranch['branch_role']) ?>)
                </span>
                <a href="<?= site_url('auth/select-branch') ?>" class="btn-link">สลับสาขา</a>
            <?php elseif (is_superadmin()): ?>
                <span class="active-branch-pill" style="background:#fef3c7; color:#92400e; border-color:#fde68a;">
                    SuperAdmin (ทุกสาขา)
                </span>
            <?php endif; ?>

            <span style="font-size: 0.875rem; color: #475569;">ผู้ใช้: <strong><?= esc($user->username ?? '') ?></strong></span>

            <form action="<?= site_url('logout') ?>" method="get" style="display:inline;">
                <button type="submit" class="btn-logout">ออกจากระบบ</button>
            </form>
        </div>
    </header>

    <main class="container">
        <div class="card">
            <h2>บริบทผู้ใช้งาน (Contextual State)</h2>
            <table>
                <tr>
                    <th style="width: 30%;">User ID / Username</th>
                    <td>#<?= esc($user->id ?? '-') ?> (<?= esc($user->username ?? '-') ?>)</td>
                </tr>
                <tr>
                    <th>Tenant ID</th>
                    <td><?= esc(current_tenant_id() ?? 'NULL (SuperAdmin)') ?></td>
                </tr>
                <tr>
                    <th>Active Branch</th>
                    <td>
                        <?php if ($activeBranch): ?>
                            <?= esc($activeBranch['name']) ?> (Code: <?= esc($activeBranch['code']) ?>)
                        <?php else: ?>
                            <em>ไม่ได้ระบุ (SuperAdmin Mode)</em>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Contextual Role ในสาขานี้</th>
                    <td><strong><?= esc($activeBranch['branch_role'] ?? ($user->isSuperAdmin() ? 'superadmin' : '-')) ?></strong></td>
                </tr>
            </table>
        </div>

        <div class="card">
            <h2>ผลการตรวจสอบสิทธิ์แบบบริบท (Contextual RBAC: auth_can)</h2>
            <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 1rem;">
                สิทธิ์จะถูกประเมินแบบ Permission-First ตาม Role ของสาขาที่กำลังทำงานอยู่
            </p>
            <table>
                <thead>
                    <tr>
                        <th>Permission Key</th>
                        <th>คำอธิบาย</th>
                        <th>สถานะการอนุญาต</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>sale.view.own</code></td>
                        <td>ดูยอดขายเฉพาะของตนเอง (.own)</td>
                        <td>
                            <?php if (auth_can('sale.view.own')): ?>
                                <span class="badge-allowed">ผ่านการอนุญาต (Allowed)</span>
                            <?php else: ?>
                                <span class="badge-denied">ปฏิเสธ (Denied)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>sale.create.branch</code></td>
                        <td>เปิดบิลขายภายในสาขา (.branch)</td>
                        <td>
                            <?php if (auth_can('sale.create.branch')): ?>
                                <span class="badge-allowed">ผ่านการอนุญาต (Allowed)</span>
                            <?php else: ?>
                                <span class="badge-denied">ปฏิเสธ (Denied)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>sale.delete.branch</code></td>
                        <td>ยกเลิกบิลขายภายในสาขา (.branch)</td>
                        <td>
                            <?php if (auth_can('sale.delete.branch')): ?>
                                <span class="badge-allowed">ผ่านการอนุญาต (Allowed)</span>
                            <?php else: ?>
                                <span class="badge-denied">ปฏิเสธ (Denied)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>sale.view.all</code></td>
                        <td>ดูยอดขายรวมทุกสาขาใน Tenant (.all)</td>
                        <td>
                            <?php if (auth_can('sale.view.all')): ?>
                                <span class="badge-allowed">ผ่านการอนุญาต (Allowed)</span>
                            <?php else: ?>
                                <span class="badge-denied">ปฏิเสธ (Denied)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code>user.manage.all</code></td>
                        <td>จัดการผู้ใช้ทั้งหมดใน Tenant (.all)</td>
                        <td>
                            <?php if (auth_can('user.manage.all')): ?>
                                <span class="badge-allowed">ผ่านการอนุญาต (Allowed)</span>
                            <?php else: ?>
                                <span class="badge-denied">ปฏิเสธ (Denied)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
