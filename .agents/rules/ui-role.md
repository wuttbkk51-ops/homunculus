---
trigger: glob
globs: "app/Views/**, public/**"
description: กฎของ view, layout, html, css, js และการแสดงผลตาม role
---

# UI / view

ขอบเขต: view, layout, html, css, js, menu ตามสิทธิ์

## view
- ใช้ layout หลักผ่าน `extend()` และ `section()` ไม่ copy โครง html ซ้ำ
- escape output ด้วย `esc()` ทุกครั้ง
- ห้ามมี query หรือ business logic ใน view รับ data ที่เตรียมแล้วจาก Controller เท่านั้น
- ฟอร์มที่ส่งด้วย POST ต้องมี `csrf_field()`
- แสดง validation error ของแต่ละ field ใกล้ช่อง input นั้น

## Role และสิทธิ์
- การซ่อนหรือแสดง element ตาม role เป็นแค่ UX ห้ามถือว่าเป็นการป้องกันสิทธิ์
  ต้องเช็คฝั่ง server ที่ Filter เสมอ (ดู `.agents/rules/auth.md`)
- menu สร้างจาก config กลางที่เดียว ห้าม hardcode การเช็ค role ใน view หลายจุด

## css / js
- ไฟล์ asset อยู่ใน `public/` แยกโฟลเดอร์ตามชนิด (`css/`, `js/`)
- ใช้ HTML ที่ semantic และรองรับหน้าจอมือถือ (responsive)
- JS เขียนแบบเรียบง่าย ถ้าจะเพิ่ม library ฝั่ง frontend ให้บอก user ก่อนและให้เหตุผล
- ไม่ฝัง `<script>` ที่มี logic ยาวใน view ให้แยกเป็นไฟล์ใน `public/js/`
