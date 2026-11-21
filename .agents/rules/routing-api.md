---
trigger: model_decision
description: ใช้เมื่อเพิ่มหรือแก้ route, API endpoint, Controller, response format หรือ validation ของ request
---

# Routing / API

## กฎของ Route
- ปิด auto routing (`$autoRoute = false` ใน `app/Config/Routing.php`) และประกาศทุก route ใน `app/Config/Routes.php`
- ห้ามเขียน Filter กำกับ auth ซ้ำซ้อนใน `Routes.php` จนรก ให้จัดการผ่าน Filter กลางใน `app/Config/Filters.php`
- 1 route ชี้ไป 1 Controller method ที่ทำ 1 action (รับ request → เรียก Service → ส่ง response)

## โครงสร้าง Admin Controllers
- Controller ใน `app/Controllers/Admin/*` กำหนด URL และ view path โดย**ไม่มีคำว่า admin**
  เช่น `App\Controllers\Admin\Users` แมปไปที่ route `/users` และใช้ view จาก `app/Views/users/`
- หน้าหลัก (Dashboard): `App\Controllers\Admin\Dashboard` แมปไปที่ route `/admin` และ `/dashboard` ส่วน route `/` ชี้ไปที่ `Home::index`

## API และ Response Format
- ข้อมูลใดก็ตามที่ส่งผ่าน Header หรือ Parameter ภายนอก ห้ามใช้ตัวเลข ID ที่เดาง่าย (เช่น Auto-increment ID) ให้เปลี่ยนไปใช้ UUID ที่ยากต่อการเดาแทนเสมอ เพื่อป้องกันการโจมตีแบบ IDOR และการรั่วไหลของข้อมูลธุรกิจ
- API ใช้ `ResponseTrait` และคืน format เดียวกันทั้งระบบ
  ```json
  { "status": "ok", "data": {}, "message": "" }
  ```
- ใช้ HTTP status code ให้ถูกความหมาย: 200 สำเร็จ, 201 สร้างแล้ว, 400 bad request, 403 ไม่มีสิทธิ์, 404 ไม่พบ, 422 validate ไม่ผ่าน
- validate request ที่ Controller ด้วย `$this->validateData($data, $rules)` โดยใช้กฎใน Model หรือ `Config\Validation` และส่งเฉพาะ `$this->validator->getValidated()` ให้ Service
- exception จาก Service แปลงเป็น response ที่ Controller หรือ exception handler จุดเดียว
- การเปลี่ยน format ของ API ให้เพิ่ม version ใหม่ (`/api/v2/...`) แทนการแก้ endpoint เดิม
