# AGENTS.md

## Stack
- CodeIgniter 4.x, PHP 8.2+, MySQL (production), SQLite3 (dev on local)
- Auth: CodeIgniter Shield

## คำสั่งหลัก (ปรับตามโปรเจกต์จริง)
- dev server: `php spark serve`
- migrate: `php spark migrate`
- test: `composer test`

## ภาษาที่ใช้
- เขียนอธิบาย ตอบ และ comment เป็นภาษาไทยเป็นหลัก
- ศัพท์เทคนิคคงเป็นภาษาอังกฤษตามที่เห็นในโค้ด ห้ามแปลเป็นสำนวนไทย และห้ามทับศัพท์เป็นอักษรไทย
  เช่น Controller, Service, Model, Filter, Migration, route, view, query, exception, validate, parameter

## ต้องอ่าน skill `ci4-design` ก่อนเริ่ม เมื่องานเป็นอย่างใดอย่างหนึ่งนี้
- เริ่มโปรเจกต์ หรือเพิ่ม feature/module ใหม่
- สร้างหรือเปลี่ยนโครงสร้างของ Service, Model, Migration
- วางโครงสร้างโฟลเดอร์ หรือแก้ `AGENTS.md` / `.agents/rules/`
- user ถามว่า "เริ่มออกแบบจากอะไร"

อ่านเฉพาะ `.agents/skills/ci4-design/SKILL.md` ส่วน `references/` อ่านเมื่อ SKILL.md สั่ง
งานอื่น (fix bug, ปรับ UI เล็กน้อย, แก้ข้อความ) ไม่ต้องอ่าน

## กฎทุกงาน
1. Controller ทำแค่ 3 อย่าง: รับ request → เรียก Service → ส่ง response
   ห้ามมี query, การคำนวณ หรือ business logic ใน Controller
2. Business logic อยู่ใน `app/Services/` (1 Service = 1 หน้าที่)
3. Query อยู่ใน `app/Models/` เท่านั้น ห้ามมีใน Controller หรือ view
4. Service ไม่อ่าน `$this->request` หรือ `$_POST` โดยตรง รับค่าผ่าน parameter
5. Fail fast: เจอข้อผิดพลาดให้ throw exception พร้อม message ที่บอกสาเหตุ
   ห้าม `catch` เปล่าๆ แล้วไม่ทำอะไร (ห้าม swallow exception)
6. โค้ดและ Migration ต้องรันได้ทั้ง MySQL และ SQLite: ใช้ Query Builder และ Forge ของ CI4
   ห้ามใช้ SQL เฉพาะ DB
7. ห้ามแก้ `system/`, `vendor/`, `writable/`
8. ห้ามใส่ secret ในโค้ด ใช้ `.env` (commit เฉพาะ `.env.example`)
9. ทุก feature ใหม่ต้องมี test อย่างน้อย 1 ตัว และรันผ่านก่อนจบงาน
10. ทำเฉพาะที่ขอ ห้าม refactor ไฟล์ที่ไม่เกี่ยวข้อง
11. ไม่แน่ใจให้ถามก่อนเดา

## กติกาการเขียน comment
1. เขียน comment เป็นภาษาไทยเป็นหลัก
2. ศัพท์เทคนิคคงเป็นภาษาอังกฤษ ไม่แปลเป็นไทย และไม่ทับศัพท์เป็นอักษรไทย
   เช่น Controller, Service, Model, query, exception, migration, filter, route, validate, parameter, return
3. comment อธิบาย "ทำไม" ไม่ใช่ "ทำอะไร" เพราะโค้ดบอกได้เองอยู่แล้ว
4. ไม่ต้อง comment สิ่งที่ชื่อ method หรือ variable บอกอยู่แล้ว
5. public method ของ Service มี docblock สั้นๆ 1-2 บรรทัด: บอกหน้าที่ และ exception ที่อาจ throw
6. ห้ามทิ้งโค้ดที่ comment ปิดไว้ (commented-out code) ให้ลบทิ้งแล้วใช้ git ย้อนดูแทน

ตัวอย่าง

```php
// ✅ order เกิน 50,000 ต้องให้ manager อนุมัติ ตามนโยบายบริษัท
if ($order->total > 50000 && ! $user->inGroup('manager')) {
    throw new ForbiddenException('ต้องให้ manager อนุมัติ');
}

// ❌ แปลศัพท์เทคนิค: "ตรวจสอบว่ายอดรวมของคำสั่งซื้อมากกว่าห้าหมื่น แล้วโยนข้อยกเว้น"
// ❌ บอกสิ่งที่โค้ดบอกอยู่แล้ว: "เช็ค total แล้ว throw exception"
```

## เมื่อไรต้องอ่านอะไร
อ่านเฉพาะไฟล์ที่ตรงกับงานปัจจุบัน ห้ามอ่านทั้งโฟลเดอร์

| งานที่ทำ | อ่านไฟล์นี้ |
|---|---|
| database, Models, Migrations, Seeds | `.agents/rules/data-model.md` |
| login, session, password, permission, filter, CodeIgniter Shield | `.agents/rules/auth.md` |
| ux-ui, html, css, js, views | `.agents/rules/ui-role.md` |
| เขียนหรือรัน test | `.agents/rules/testing.md` |
| เพิ่มหรือแก้ route, API endpoint, request validation | `.agents/rules/routing-api.md` |

## เมื่อเพิ่มกฎใหม่
- กฎที่ใช้ทุกงาน → ใส่ในไฟล์นี้ (ต้องสั้น)
- กฎเฉพาะเรื่อง → ใส่ใน `.agents/rules/<หัวข้อ>.md` แล้วเพิ่มแถวในตารางด้านบน
- ไฟล์ใน `.agents/rules/` ต้องขึ้นต้นด้วย frontmatter ที่มี `trigger` (`always_on` | `model_decision` | `glob` | `manual`)
  ถ้าไม่มี Antigravity จะทิ้งไฟล์นั้นเงียบๆ ดูรูปแบบจากไฟล์เดิมในโฟลเดอร์
