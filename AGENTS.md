# AGENTS.md

## Stack
- CodeIgniter 4.7+, PHP 8.2+, MySQL (production), SQLite3 (dev on local)
- Auth: CodeIgniter Shield

## คำสั่งหลัก
- dev server: `php spark serve`
- migrate: `php spark migrate` (เช็คสถานะด้วย `php spark migrate:status`)
- route check: `php spark routes` (เช็ค Filter ด้วย `php spark filter:check <GET|POST> <route>`)
- clear cache: `php spark cache:clear`
- test: `composer test`
- generate code: การสร้างไฟล์ Controller, Model, Migration, Seeder และอื่นๆ ให้สร้างผ่าน spark command (`php spark make:*` เช่น `php spark make:controller`, `php spark make:model`, `php spark make:migration`, `php spark make:seeder`) เป็นอันดับแรกเสมอ เพื่อโครงสร้างไฟล์ที่เป็นมาตรฐานของ CodeIgniter 4 แล้วจึงปรับแก้โค้ดให้ตรงตามกฎของโปรเจกต์ (เช่น เปลี่ยน Model ให้สืบทอดจาก `TenantModel`)

## ภาษาที่ใช้
- เขียนอธิบาย ตอบ และ comment เป็นภาษาไทยเป็นหลัก
- ศัพท์เทคนิคคงเป็นภาษาอังกฤษตามที่เห็นในโค้ด ห้ามแปลเป็นสำนวนไทย และห้ามทับศัพท์เป็นอักษรไทย
  เช่น Controller, Service, Model, Filter, Migration, route, view, query, exception, validate, parameter
- ชื่อ class, method, variable, column เป็นภาษาอังกฤษเสมอ
- ระบบข้อความและการแสดงผล (exception message, validation message, response message, UI/flash message):
  - ห้าม hardcode ข้อความในโค้ดโดยเด็ดขาด เช่น `throw new InvalidArgumentException('ไม่พบข้อมูลบุคคล');`
  - ให้ใช้ระบบ Language ของ CodeIgniter 4 ผ่าน `lang('moduleName.key')` เสมอ (key เป็น camelCase เช่น `Person.notFound`)
  - รูปแบบการตั้งชื่อไฟล์: ใช้ชื่อโมดูล `moduleName` เดียวกันทั้งระบบ ไม่ว่าจะเรียกใช้จาก Controller, View, Service, Model หรือ Filter (เช่น `app/Language/{en,th}/Person.php`)
  - ทุกครั้งที่มีการเพิ่มหรือแก้ไขข้อความ ต้องเขียนไฟล์ภาษาพร้อมกัน 2 ภาษาเสมอ ทั้งภาษาอังกฤษ (`app/Language/en/`) และภาษาไทย (`app/Language/th/`)
  - log message (`log_message()`) ไม่ต้องผ่าน `lang()`

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
3. Multi-tenancy: ทุก table ต้องมี `tenant_id` และ Model ที่สร้างใหม่ต้องสืบทอดจาก `TenantModel` เท่านั้น
   ยกเว้น table ระบบ (`migrations`, table ของ Shield ที่ไม่ผูก tenant) ให้ถามก่อนสร้างหรือแก้
   `tenant_id` มาจาก <ระบุที่มา เช่น Filter ตั้งค่าจาก session/subdomain> ห้ามรับจาก request ตรงๆ
4. Query อยู่ใน `app/Models/` เท่านั้น ห้ามมีใน Controller หรือ view
5. Service ไม่อ่าน `$this->request` หรือ `$_POST` โดยตรง รับค่าผ่าน parameter
6. เขียนโค้ดตามแนวทาง Ponytail: Clean, Minimal, Reuse, Modular
   แก้ปัญหาด้วยโค้ดที่สั้นและตรงจุดที่สุด ห้ามสร้าง abstraction หรือเขียนโค้ดเผื่ออนาคตที่ยังไม่จำเป็น (YAGNI)
7. ใช้ Cache (`cache()`) สำหรับข้อมูลที่ถูกเรียกบ่อยหรือทุก request เช่น `tenant_id`, user status และ user permissions
   cache key ต้องมี `tenant_id` เสมอ (กันข้อมูลข้าม tenant ปนกัน) และต้อง invalidate เมื่อข้อมูลต้นทางเปลี่ยน เช่น สิทธิ์ถูกแก้
8. Fail fast: เจอข้อผิดพลาดให้ throw exception พร้อม message ที่บอกสาเหตุ
   ห้าม `catch` เปล่าๆ แล้วไม่ทำอะไร (ห้าม swallow exception)
   catch ได้เฉพาะเมื่อ rethrow หรือแปลงเป็น exception/response ที่เหมาะสมที่ boundary (Controller, Filter, exception handler)
9. โค้ดและ Migration ต้องรันได้ทั้ง MySQL และ SQLite: ใช้ Query Builder และ Forge ของ CI4
   ห้ามใช้ SQL เฉพาะ DB (เช่น ENUM, JSON function, FULLTEXT) และ Migration ที่ alter/drop column หรือ foreign key ต้องลองรันบนทั้งสอง DB ก่อน commit
10. ห้ามแก้ `system/`, `vendor/` และห้ามแก้หรือ commit ไฟล์ใน `writable/` (ระบบเขียนเองตอน runtime)
11. ห้ามใส่ secret ในโค้ด ใช้ `.env` (commit เฉพาะ `.env.example`)
12. ทุก feature ใหม่ต้องมี test อย่างน้อย 1 ตัว, รันผ่านก่อนส่งงาน และ commit หลังจาก test ผ่านแล้วเท่านั้น
    feature ที่อ่านหรือเขียนข้อมูลต้องมี test ยืนยันว่า tenant A เข้าถึงข้อมูลของ tenant B ไม่ได้
13. ทำเฉพาะที่ขอ ห้าม refactor ไฟล์ที่ไม่เกี่ยวข้อง
14. ไม่แน่ใจให้ถามก่อนเดา โดยเฉพาะงานที่กระทบ schema, สิทธิ์ หรือการลบข้อมูล
15. ในช่วง dev <ระบุว่าจบเมื่อไหร่ เช่น จนกว่าจะ deploy production ครั้งแรก> ห้ามเก็บ code เก่าไว้เพื่อ backward compatibility
    เมื่อเปลี่ยนชื่อหรือเปลี่ยนวิธีเรียก function/method/class ให้แก้ทุกจุดที่เรียกใช้ให้เป็นชื่อใหม่
    ห้ามสร้าง alias, wrapper หรือ method เก่าที่ forward ไปหาตัวใหม่ (ใช้ `grep` หาจุดเรียกใช้ให้ครบก่อน)
16. ทุกครั้งที่เริ่มหรือเพิ่ม feature หรือ core ใดๆ ต้องแยก git branch เสมอ (หากไม่มั่นใจให้ถามผู้ใช้ก่อน)
17. ความปลอดภัยของ Identifier: ข้อมูลใดก็ตามที่ Client มองเห็นหรือส่งกลับมาได้ (URL, route parameter, query string, form, JSON body, Header, cookie)
    ห้ามใช้ Auto-increment ID หรือตัวเลขที่เดาง่าย ให้ใช้ UUID แทน เพื่อป้องกัน IDOR และ Information Leak
    - ภายใน DB (primary key, foreign key, join) ยังใช้ Integer ID ตามปกติ
    - table ที่ถูกอ้างอิงจากภายนอกมี column `uuid` (unique index) สร้างอัตโนมัติใน `TenantModel` ผ่าน callback ก่อน insert ห้ามสร้างเองใน Controller หรือ Service
    - ใช้ UUID v4 และ library/helper ตัวเดียวกันทั้งระบบ
    - Model ต้องมี method หา record จาก `uuid` และ response ห้ามมี Integer ID หลุดออกไป
    - UUID ไม่ใช่ authorization: ทุก request ยังต้องเช็ค `tenant_id` และสิทธิ์เจ้าของข้อมูลเสมอ

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
    throw new ForbiddenException(lang('Order.approvalRequired'));
}

// ❌ แปลศัพท์เทคนิค: "ตรวจสอบว่ายอดรวมของคำสั่งซื้อมากกว่าห้าหมื่น แล้วโยนข้อยกเว้น"
// ❌ บอกสิ่งที่โค้ดบอกอยู่แล้ว: "เช็ค total แล้ว throw exception"
```

## เมื่อไรต้องอ่านอะไร
อ่านเฉพาะไฟล์ที่ตรงกับงานปัจจุบัน ห้ามอ่านทั้งโฟลเดอร์

| งานที่ทำ | อ่านไฟล์นี้ |
|---|---|
| database, Models, TenantModel, Migrations, Seeds, การ bootstrap ระบบ (ข้อมูลเริ่มต้น tenant/admin) | `.agents/rules/data-model.md` |
| login, session, password, permission, Filter, Cache ข้อมูลสิทธิ์ | `.agents/rules/auth.md` |
| ux-ui, html, css, js, views | `.agents/rules/ui-role.md` |
| เขียนหรือรัน test | `.agents/rules/testing.md` |
| route, API endpoint, Controller, routing admin/dashboard | `.agents/rules/routing-api.md` |

## การอ่านไฟล์ใน `.agents/rules/`
- ทุก agent (Antigravity, Claude Code, Codex ฯลฯ) ให้อ่านไฟล์ตามตารางด้านบนโดยตรง
- `trigger` ใน frontmatter ใช้เฉพาะ Antigravity ส่วน agent อื่นให้ข้ามได้ และห้ามลบทิ้ง
- งานที่เข้าหลายแถวในตาราง ให้อ่านทุกไฟล์ที่ตรง

## เมื่อเพิ่มกฎใหม่
- กฎที่ใช้ทุกงาน → ใส่ในไฟล์นี้ (ต้องสั้น)
- กฎเฉพาะเรื่อง → ใส่ใน `.agents/rules/<หัวข้อ>.md` แล้วเพิ่มแถวในตารางด้านบน
- ไฟล์ใน `.agents/rules/` ต้องขึ้นต้นด้วย frontmatter ที่มี `trigger` (`always_on` | `model_decision` | `glob` | `manual`)
  ถ้าไม่มี Antigravity จะทิ้งไฟล์นั้นเงียบๆ ดูรูปแบบจากไฟล์เดิมในโฟลเดอร์