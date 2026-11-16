<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บัญชีองค์กรถูกระงับการใช้งาน - Homunculus</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; }
        .card { background: #ffffff; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); width: 100%; max-width: 28rem; padding: 2.5rem 2rem; text-align: center; }
        .icon-box { width: 4rem; height: 4rem; background-color: #fee2e2; color: #dc2626; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem; }
        h1 { font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem; }
        p { font-size: 0.875rem; color: #6b7280; line-height: 1.5; margin-bottom: 2rem; }
        .btn-home { display: inline-block; background-color: #4b5563; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 0.875rem; padding: 0.75rem 1.5rem; border-radius: 0.5rem; transition: background-color 0.2s; }
        .btn-home:hover { background-color: #374151; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-box">⚠️</div>
        <h1>บัญชีองค์กรถูกระงับการใช้งาน</h1>
        <p>บัญชีองค์กร (Tenant) ของท่านไม่พร้อมให้บริการในขณะนี้ เนื่องจากสถานะถูกระงับหรือพ้นกำหนดระยะเวลาการใช้งาน กรุณาติดต่อผู้ดูแลระบบเพื่อต่ออายุหรือขอรับความช่วยเหลือ</p>
        <a href="<?= site_url('login') ?>" class="btn-home">กลับสู่หน้าเข้าสู่ระบบ</a>
    </div>
</body>
</html>
