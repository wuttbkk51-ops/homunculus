---
trigger: glob
globs: "app/Models/**, app/Entities/**, app/Database/**, app/Services/BootstrapService.php"
description: กฎของ database, TenantModel, Multi-tenancy, Migration, Seeder และ Bootstrap (ต้องรันได้ทั้ง MySQL และ SQLite)
---

# Data model

ขอบเขต: schema, Migration, Seeder, Model, Entity, Multi-tenancy, Web Bootstrap

## Multi-tenancy
- ทุก table ต้องมี column `tenant_id` (ยกเว้น table ของ Shield หรือ table กลางระดับระบบ)
- ทุก Model ที่สร้างใหม่**ต้องสืบทอดจาก `App\Models\TenantModel` เท่านั้น** ห้ามสืบทอดจาก `CodeIgniter\Model` โดยตรง
- `TenantModel` ต้องผูก auto where `tenant_id` ให้ทุก action (find, insert, update, delete) อัตโนมัติ
- ใช้ Cache (`cache()`) สำหรับ `tenant_id` ที่ใช้บ่อยหรือเรียกทุก request เพื่อลด query
- ค่า `tenant_id = 0` ใช้เป็น Sentinel Value แทนข้อมูลระดับ Global (ระบบส่วนกลาง) สำหรับ table ที่รองรับข้อมูล Global ให้ใช้เพียง `INDEX(tenant_id)` ไม่ต้องผูก Foreign Key ไปยัง table `tenants`
- Composite Index (Key คู่) ใน Multi-tenancy ต้องเรียงโดยเอา `tenant_id` ไว้ข้างหน้าเสมอ เช่น `(tenant_id, id)` หรือ `(tenant_id, user_id)` เพื่อให้ query วิ่งผ่าน Index ได้เต็มประสิทธิภาพตาม Leftmost Prefix Rule

## Migration
- แก้ schema ผ่าน Migration ใหม่เท่านั้น ห้ามแก้ Migration ที่รันไปแล้ว และต้องมี `down()` ที่ย้อนกลับได้จริง
- สร้าง table และ column ด้วย Forge ของ CI4 เท่านั้น ห้ามเขียน SQL ดิบ
- ชื่อ table เป็น snake_case พหูพจน์ (`orders`), ชื่อ column เป็น snake_case (`created_at`)
- foreign key ระบุ `ON DELETE` ให้ชัดเจนทุกครั้ง

## ต้องรันได้ทั้ง MySQL (production) และ SQLite (dev)
- ห้ามใช้ syntax เฉพาะ DB เช่น `ENUM`, `ENGINE=`, `ON UPDATE CURRENT_TIMESTAMP`
  ใช้ `VARCHAR` + validation แทน `ENUM`, ใช้ `TEXT` แทนชนิด `JSON`
- SQLite แก้หรือลบ column ได้จำกัด: ถ้า Migration มีการ alter table ต้องทดสอบทั้งสอง DB ก่อน merge
- ตั้ง charset เป็น `utf8mb4` ฝั่ง MySQL และเปิด `foreignKeys` ใน `Config\Database` ฝั่ง SQLite3

## Model และ Entity
- กำหนด `$allowedFields` ทุก Model ห้ามเปิดรับทุก field
- กฎ validate ของ data อยู่ใน Model (`$validationRules`) ไม่กระจายใน Controller
- ใช้ `$useTimestamps = true` สำหรับ table ที่มี `created_at` / `updated_at`
- งานที่แตะหลาย table พร้อมกันให้ครอบด้วย transaction (`$db->transStart()`) ใน Service ไม่ใช่ Model

## Seeder และ Web Bootstrap (Shared Host)
- มี Seeder กลาง (เช่น `seedInit` หรือ `InitSeeder`) เป็น endpoint หลักในการเรียกทุก seed ย่อย จบในที่เดียว
- Seeder ต้อง idempotent รันซ้ำได้โดย data ไม่ซ้ำ
- กรณี deploy บน shared host ที่ไม่มี SSH/terminal ให้ใช้ `App\Services\BootstrapService` ทำหน้าที่ setup database, migrate และรัน seed
- มี `App\Controllers\Setup` เป็นตัวกลางรับ request เรียกใช้ `BootstrapService` และต้องมีระบบป้องกันความปลอดภัย (เช่น ตรวจ token ลับ หรือปิด route ทันทีหลังรันสำเร็จ)

## table ของ Shield
- ห้ามแก้ table ที่ Shield สร้างโดยตรง ถ้าต้องเก็บ data เพิ่มของ user ให้สร้าง table ใหม่ผูกกับ `users.id`
