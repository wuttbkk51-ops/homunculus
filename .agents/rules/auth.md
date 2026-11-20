---
trigger: model_decision
description: ใช้เมื่อทำงานเกี่ยวกับ login, session, password, permission, group, Filter หรือ CodeIgniter Shield
---

# Auth (CodeIgniter Shield)

ขอบเขต: login, session, password, permission, Filter, Cache ข้อมูลสิทธิ์

## หลักการ Auth
- ใช้ Shield เป็น auth หลัก ห้ามเขียนระบบ login ซ้อน
- password ให้ Shield จัดการ ห้ามเก็บ password แบบ plain text หรือเรียก `password_hash()` เอง
- กำหนด group และ permission ที่เดียวใน `app/Config/AuthGroups.php` ห้าม hardcode ชื่อ group ในโค้ด
- ค่า secret ทั้งหมดอยู่ใน `.env` และห้าม log password, token หรือ session id

## Filter (Middleware) ทุก request
- ห้ามเขียน Filter กำกับ auth ซ้ำซ้อนใน `Config/Routes.php` จนรก
- ตั้งค่า Filter ใน `app/Config/Filters.php` (ที่ `$globals['before']` หรือ `$filters`) ให้เช็ค auth ทุก request อัตโนมัติ
- ยกเว้นเฉพาะ route สาธารณะผ่าน `except` เช่น `['login*', 'register*', 'setup*']`
- Controller ทั้งหมดใน `app/Controllers/Admin/*` ต้องผ่าน auth เสมอ

## Cache ข้อมูลสิทธิ์และสถานะ
- ข้อมูลที่ถูกเรียกบ่อยหรือตรวจทุก request เช่น `user-status`, `user-permissions` ให้ดึงผ่าน Cache (`cache()`) เพื่อลดภาระ DB
- เคลียร์ Cache ทันทีเมื่อสถานะหรือ permission ของ user มีการเปลี่ยนแปลง

## การตรวจสิทธิ์ในโค้ด
- ใน Service ใช้ `$user->can('...')` เมื่อต้องเช็คสิทธิ์ตามเงื่อนไขของ data
- message ตอน login ไม่สำเร็จต้องไม่เจาะจงว่า email หรือ password ผิดข้อใดข้อหนึ่ง
- การซ่อน element ตาม role ในหน้าจอเป็นแค่ UX (ดู `.agents/rules/ui-role.md`) ห้ามถือว่าเป็นการป้องกันสิทธิ์
