---
trigger: model_decision
description: ใช้เมื่อเพิ่มหรือแก้ route, API endpoint, response format หรือ validation ของ request
---

# Routing / API

- ปิด auto routing (`$routes->setAutoRoute(false)`) และประกาศทุก route ใน `app/Config/Routes.php`
- จัด route เป็น group ตามหัวข้อ และผูก Filter ที่ระดับ group
- 1 route ชี้ไป 1 Controller method ที่ทำ 1 action
- API ใช้ `ResponseTrait` และคืน response format เดียวกันทั้งระบบ
  ```json
  { "status": "ok", "data": {}, "message": "" }
  ```
- ใช้ HTTP status code ให้ถูกความหมาย: 200 สำเร็จ, 201 สร้างแล้ว, 404 ไม่พบ, 403 ไม่มีสิทธิ์, 422 validate ไม่ผ่าน
- validate request ที่ขอบระบบ (Controller) ด้วยกฎที่อยู่ใน Model หรือ `Config\Validation` ไม่เขียนกระจาย
- error จาก Service (exception) แปลงเป็น response ที่ Controller หรือ exception handler ที่เดียว
- เปลี่ยน format ของ API ที่มี client ใช้แล้ว ให้เพิ่ม version ใหม่ (`/api/v2/...`) แทนการแก้ของเดิม
