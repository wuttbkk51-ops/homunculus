---
trigger: model_decision
description: ใช้เมื่อทำงานเกี่ยวกับ login, session, password, permission, group, Filter หรือ CodeIgniter Shield
---

# Auth (CodeIgniter Shield)

ขอบเขต: login, session, password, permission, Filter

- ใช้ Shield เป็น auth หลัก ห้ามเขียนระบบ login เองซ้อน
- password ให้ Shield จัดการ ห้ามเรียก `password_hash()` หรือเก็บ password เอง ห้ามเก็บ password แบบ plain text
- การเช็คสิทธิ์ทำที่ route ผ่าน Filter (`group`, `permission`, `session`) ไม่ใช่ใน Controller
  ```php
  // ผูก Filter ที่ route group ให้ครอบทั้งกลุ่ม ไม่เช็คทีละ method
  $routes->group('admin', ['filter' => 'group:admin'], static function ($routes) {
      // ...
  });
  ```
- กำหนด group และ permission ที่เดียวใน `app/Config/AuthGroups.php` ห้าม hardcode ชื่อ group กระจายในโค้ด
- ใน Service ใช้ `$user->can('...')` เมื่อต้องเช็คสิทธิ์ตามเงื่อนไขของ data (เช่นแก้ได้เฉพาะของตัวเอง)
- message ตอน login ไม่สำเร็จต้องไม่บอกว่า email หรือ password ผิดข้อใดข้อหนึ่ง
- ห้าม log password, token หรือ session id
- ค่า config ที่เป็น secret (เช่น key ของ JWT) อยู่ใน `.env` เท่านั้น
- เปลี่ยน schema ของ table ที่เกี่ยวกับ user ต้องทำผ่าน Migration และอ่าน `.agents/rules/data-model.md`
- การซ่อนหรือแสดง element ตาม role ในหน้าจออยู่ใน `.agents/rules/ui-role.md` (เป็นแค่ UX ไม่ใช่การป้องกันสิทธิ์)
