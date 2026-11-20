---
trigger: glob
globs: "app/Models/**, app/Entities/**, app/Database/**"
description: กฎของ database, Model, Entity, Migration, Seeder (ต้องรันได้ทั้ง MySQL และ SQLite)
---

# Data model

ขอบเขต: schema, Migration, Seeder, Model, Entity

## Migration
- แก้ schema ผ่าน Migration ใหม่เท่านั้น ห้ามแก้ Migration ที่เคยรันไปแล้ว
- ทุก Migration ต้องมี `down()` ที่ย้อนกลับได้จริง
- สร้าง table และ column ด้วย Forge ของ CI4 เท่านั้น ห้ามเขียน SQL ดิบ
- ชื่อ table เป็น snake_case พหูพจน์ (`orders`), ชื่อ column เป็น snake_case (`created_at`)
- foreign key ระบุ `ON DELETE` ให้ชัดเจนทุกครั้ง

## ต้องรันได้ทั้ง MySQL (production) และ SQLite (dev)
- ห้ามใช้ชนิดหรือ option เฉพาะ DB เช่น `ENUM`, `ENGINE=`, `ON UPDATE CURRENT_TIMESTAMP`
  ใช้ `VARCHAR` + validation แทน `ENUM`, ใช้ `TEXT` แทนชนิด `JSON`
- SQLite แก้หรือลบ column ได้จำกัด: ถ้า Migration ต้อง alter table ให้ทดสอบทั้งสอง DB ก่อน merge
- การเทียบข้อความ (`=`, `LIKE`) เรื่อง case sensitivity ต่างกันระหว่างสอง DB
  ห้ามพึ่งพฤติกรรมนี้ ให้ normalize ก่อนเก็บ (เช่น email เป็นตัวพิมพ์เล็ก)
- ตรวจว่า `foreignKeys` เปิดอยู่ใน `Config\Database` ของ SQLite3 เพื่อให้ constraint ทำงานเหมือน production
- ตั้ง charset เป็น `utf8mb4` ฝั่ง MySQL

## Model และ Entity
- กำหนด `$allowedFields` ทุก Model ห้ามเปิดรับทุก field
- กฎ validate ของ data อยู่ใน Model (`$validationRules`) ไม่กระจายใน Controller
- ใช้ `$useTimestamps = true` สำหรับ table ที่ต้องมี `created_at` / `updated_at`
- งานที่แตะหลาย table พร้อมกันให้ครอบด้วย transaction (`$db->transStart()`) และทำใน Service ไม่ใช่ Model

## Seeder
- ต้องรันซ้ำได้โดยไม่เกิด data ซ้ำ (idempotent)
- แยก seeder สำหรับ dev ออกจาก data ที่ production ต้องใช้จริง

## table ของ Shield
- ห้ามแก้ table ที่ Shield สร้างโดยตรง ถ้าต้องเก็บ data เพิ่มของ user ให้สร้าง table ใหม่ที่ผูกกับ `users.id`
