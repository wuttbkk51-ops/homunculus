---
name: ci4-design
description: แนวทางออกแบบและเริ่มโปรเจกต์ CodeIgniter 4 (CI4) ด้วยหลัก Unix/Linux philosophy (single responsibility, composition, simplicity) และลำดับการเริ่มออกแบบซอฟต์แวร์ (problem → user → data → MVP → constraint → module → stack). ใช้ skill นี้ทุกครั้งที่ผู้ใช้จะเริ่มโปรเจกต์ CI4 ใหม่, ออกแบบ feature/module/Service/Model/Controller/Migration ของ CI4, วางโครงสร้างโฟลเดอร์, เขียนหรือปรับ AGENTS.md สำหรับ CI4, หรือถามว่า "เริ่มออกแบบจากอะไร" แม้ไม่ได้พูดถึงหลัก Unix ตรงๆ
---

# CI4 Design (Unix-style)

ใช้ตอน **ออกแบบ** ก่อนลงมือเขียนโค้ด ไม่ใช่ตอน debug ถ้างานเป็น fix bug เล็กน้อย ไม่ต้องไล่ตามขั้นตอนทั้งหมด

## ภาษาที่ใช้

เขียนอธิบายเป็นภาษาไทย แต่ **ไม่แปลศัพท์เทคนิค** ให้คงคำอังกฤษตามที่เห็นในโค้ด เช่น Controller, Service, Model, Entity, Filter, Migration, Seeder, route, view, query, exception, validate, parameter, interface, module, schema, config, test
ห้ามทับศัพท์เป็นอักษรไทย (เช่น "คอนโทรลเลอร์") และห้ามแปลเป็นสำนวนไทย (เช่น "บาง", "กลืน error")

## ลำดับการทำงาน

1. **ตรวจว่าข้อมูลพอไหม** ก่อนออกแบบต้องตอบ 5 ข้อนี้ได้ ถ้าผู้ใช้ยังไม่บอก ให้ถามรวมครั้งเดียว ไม่เกิน 3 คำถาม
   - ปัญหาคืออะไร และใครเป็น user
   - ข้อมูลหลัก (data) มีอะไรบ้าง
   - MVP คืออะไร และ **อะไรที่ไม่ทำ**
   - constraint: จำนวน user, ต้องใช้ offline ไหม, ต้องเชื่อมระบบอื่นไหม
   รายละเอียดและกับดัก: `references/project-kickoff.md`
2. **ออกแบบ data ก่อนหน้าจอ** ร่าง table และความสัมพันธ์ แล้วค่อยคิด Model/Migration
3. **แบ่ง module ตามหน้าที่เดียว** แต่ละ module มี interface ชัดเจน (public method ของ Service) และไม่ import ข้ามกันตรงๆ
4. **เลือกโครงสร้างโฟลเดอร์** ตามขนาดงาน (ดูด้านล่าง)
5. **ทำ vertical slice** เลือก flow เดียวให้ทำงานครบตั้งแต่ route → Controller → Service → Model → view แล้วค่อยขยาย
6. **บันทึกกฎลง AGENTS.md** กฎที่ใช้ทุกงานอยู่ที่ root ส่วนกฎเฉพาะเรื่องแยกไฟล์ใน `.agents/rules/` พร้อมตารางบอกว่าเมื่อไรต้องอ่านไฟล์ไหน

## หลัก Unix ที่แปลงเป็นกฎใน CI4

รายละเอียดเต็ม: `references/unix-principles.md` สรุปที่ใช้ทุกงาน:

| หลัก | กฎใน CI4 |
|---|---|
| Single responsibility | 1 Service = 1 หน้าที่, 1 Controller method = 1 action |
| Composition | Service เรียก Service อื่นผ่าน public method ห้ามแตะ Model ของ module อื่นตรงๆ |
| Separation of policy and mechanism | business logic อยู่ Service, การเก็บ data อยู่ Model, การแสดงผลอยู่ view |
| Simplicity | ใช้ของมาตรฐาน CI4 ก่อน (Filter, Validation, Entity) อย่าสร้างเอง |
| Representation | ใช้ data (lookup table, config) แทน if/else ยาวๆ |
| Least surprise | ตั้งชื่อตาม convention ของ CI4 และใช้ response format เดียวกันทั้งระบบ |
| Fail fast | ผิดปกติให้ throw exception พร้อม message ที่บอกสาเหตุ ห้าม swallow exception |
| Silence | ไม่ log หรือ echo สิ่งที่ไม่ผิดปกติ |
| Economy | เวลาคนแพงกว่าเวลาเครื่อง เขียนให้อ่านง่ายก่อน |
| Optimization | ทำให้ถูกก่อน วัดผลก่อนแล้วค่อยปรับให้เร็ว |

## เลือกโครงสร้างตามขนาดงาน

- **เล็ก (1-3 feature, ทำคนเดียว)**: โครงสร้างมาตรฐานของ CI4 (`Controllers/Models/Services/Filters/Views`) และแยกกฎเป็นไฟล์ตามหัวข้อ
- **กลางถึงใหญ่ (หลาย feature แยกกันชัด)**: CI4 Modules (`app/Modules/<Name>/`) แต่ละ module มี Controllers/Models/Views ของตัวเอง และตั้งค่า psr4 ใน `Config\Autoload`

เริ่มจากแบบเล็กก่อนเสมอ ย้ายไป Modules เมื่อ feature เริ่มปนกันจริง ไม่ต้องออกแบบเผื่อล่วงหน้า

## สิ่งที่ต้องส่งมอบหลังออกแบบ

สรุปสั้นๆ ให้ user ตรวจก่อนเขียนโค้ด:

1. problem และ user (2-3 บรรทัด)
2. data หลัก (table และความสัมพันธ์)
3. รายการ module และหน้าที่เดียวของแต่ละตัว
4. MVP และสิ่งที่ **ไม่ทำ**
5. โครงสร้างโฟลเดอร์ที่เลือก พร้อมเหตุผล 1 บรรทัด
6. ร่าง `AGENTS.md` และรายการไฟล์กฎที่ต้องมี

## ข้อห้าม

- ห้ามเลือก framework หรือ library เพิ่มก่อนเข้าใจ problem
- ห้ามออกแบบเผื่ออนาคตที่ยังไม่มี user จริงรองรับ
- ห้ามเริ่มจากหน้าจอแล้วค่อยนึกถึง schema
- ห้ามยัดกฎทั้งหมดลง AGENTS.md ไฟล์เดียว ให้แยกเป็นไฟล์ตามหัวข้อและให้ root ชี้ทาง
- ห้ามแปลศัพท์เทคนิคเป็นไทยใน comment, เอกสาร หรือคำตอบ
