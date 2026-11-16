<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกสาขาเพื่อเข้าปฏิบัติงาน - Homunculus</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; }
        .card { background: #ffffff; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1); width: 100%; max-width: 28rem; padding: 2rem; }
        h1 { font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; text-align: center; }
        p.subtitle { font-size: 0.875rem; color: #6b7280; text-align: center; margin-bottom: 1.5rem; }
        .branch-list { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; }
        .branch-item { display: flex; align-items: center; justify-content: space-between; border: 1.5px solid #e5e7eb; border-radius: 0.5rem; padding: 0.875rem 1rem; cursor: pointer; transition: all 0.2s; }
        .branch-item:hover { border-color: #3b82f6; background-color: #eff6ff; }
        .branch-item input[type="radio"] { margin-right: 0.75rem; accent-color: #2563eb; width: 1.125rem; height: 1.125rem; }
        .branch-info { flex: 1; }
        .branch-name { font-weight: 600; color: #1f2937; font-size: 0.95rem; }
        .branch-meta { font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem; }
        .role-badge { display: inline-block; font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 9999px; background-color: #e0f2fe; color: #0369a1; text-transform: uppercase; }
        .btn-submit { width: 100%; background-color: #2563eb; color: #ffffff; font-weight: 600; font-size: 0.875rem; padding: 0.75rem 1rem; border: none; border-radius: 0.5rem; cursor: pointer; transition: background-color 0.2s; }
        .btn-submit:hover { background-color: #1d4ed8; }
        .alert { background-color: #fef2f2; border: 1px solid #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>เลือกสาขาเพื่อเข้าปฏิบัติงาน</h1>
        <p class="subtitle">คุณมีสิทธิ์เข้าถึงหลายสาขา กรุณาเลือกสาขาที่ต้องการทำรายการ</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <form action="<?= site_url('auth/select-branch') ?>" method="post">
            <?= csrf_field() ?>

            <div class="branch-list">
                <?php foreach ($branches as $index => $b): ?>
                    <label class="branch-item" for="branch_<?= esc($b['branch_id']) ?>">
                        <input type="radio" name="branch_id" id="branch_<?= esc($b['branch_id']) ?>" value="<?= esc($b['branch_id']) ?>" <?= ($index === 0) ? 'checked' : '' ?>>
                        <div class="branch-info">
                            <div class="branch-name"><?= esc($b['branch_name']) ?></div>
                            <div class="branch-meta">รหัสสาขา: <?= esc($b['branch_code']) ?></div>
                        </div>
                        <span class="role-badge"><?= esc($b['branch_role']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-submit">เข้าสู่ระบบสาขา</button>
        </form>
    </div>
</body>
</html>
