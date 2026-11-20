# หลัก Unix/Linux สำหรับออกแบบ CI4

อ่านไฟล์นี้เมื่อต้องตัดสินใจเรื่องการแบ่งหน้าที่ของโค้ด หรือเขียนกฎลง AGENTS.md
(เขียนอธิบายเป็นไทย แต่ศัพท์เทคนิคคงเป็นภาษาอังกฤษ)

## แกนหลัก 3 ข้อ
1. Single responsibility: โปรแกรมหรือคลาสหนึ่งตัวทำหน้าที่เดียวและทำให้ดี
2. Composition: ส่วนต่างๆ ทำงานร่วมกันได้ผ่าน interface ที่ชัดเจน
3. Simple data format: ใช้รูปแบบ data ที่เรียบง่ายและคงที่เป็นตัวกลาง (ใน CI4 คือ array, Entity หรือ JSON ที่ format ไม่เปลี่ยน)

## หลักทั้งหมด → ข้อปฏิบัติใน CI4

| หลัก | ข้อปฏิบัติ |
|---|---|
| Modularity | แบ่งเป็น Service/module เล็กๆ ติดต่อกันผ่าน public method |
| Clarity | ชื่อ method บอกพฤติกรรม (`approveOrder` ไม่ใช่ `process`) |
| Composition | Service ออกแบบให้ถูกเรียกได้จาก Controller, `spark` command และ test เท่ากัน |
| Separation | policy (business logic) อยู่ Service / mechanism (เก็บ data) อยู่ Model |
| Simplicity | ไม่เพิ่ม abstraction จนกว่าโค้ดจะซ้ำ 3 ครั้ง |
| Parsimony | ถ้าทำเล็กได้ ไม่ทำใหญ่ ไฟล์หรือคลาสที่เกิน ~300 บรรทัดให้พิจารณาแยก |
| Transparency | log เหตุการณ์สำคัญ, error message บอกสาเหตุจริง, debug ได้โดยไม่ต้องเดา |
| Robustness | validate ที่ขอบระบบ (Controller/API) แล้วเชื่อ data ภายใน |
| Representation | ใช้ lookup table หรือ config แทน if/else ยาวๆ |
| Least surprise | ใช้ convention ของ CI4 และ response format เดียวทั้งระบบ |
| Silence | ไม่ผิดปกติ ไม่ต้อง log หรือแจ้งเตือน |
| Fail fast | ผิดปกติให้ throw exception ทันที อย่า swallow exception |
| Economy | เลือกโค้ดที่คนอ่านง่ายก่อนโค้ดที่เร็วกว่าเล็กน้อย |
| Generation | งานซ้ำๆ (CRUD, boilerplate ของ Migration) ใช้ `php spark make:*` หรือ script แทนการเขียนมือ |
| Optimization | ทำให้ถูกก่อน วัดด้วย data จริงก่อนค่อยปรับให้เร็ว |
| Diversity | อย่าล็อกกับวิธีเดียว เลือกเครื่องมือที่เหมาะกับงาน |
| Extensibility | เปิดจุดต่อยอดผ่าน Events, Filter, Config แทนการแก้โค้ดแกน |

## ธรรมเนียมเชิงปฏิบัติ (ใช้เป็น checklist)

- Everything is a file: config เป็นไฟล์ข้อความ (`.env`, `Config/*.php`) เก็บใน git ได้
- stdin/stdout/stderr: `spark` command ส่งผลลัพธ์ออก stdout และ error ออก stderr
- Exit code: 0 คือสำเร็จ ค่าอื่นคือล้มเหลว (สำคัญเมื่อ `spark` command ถูกเรียกจาก cron หรือ CI)
- command ต้องคาดเดาได้: มี `--help` และใช้ flag มาตรฐาน

## ตัวอย่างกฎที่ตรวจได้ (ใส่ใน AGENTS.md ได้เลย)

- Controller method ไม่เกิน ~20 บรรทัด และไม่มี query
- Service ไม่ใช้ `$this->request` หรือ `$_POST` โดยตรง รับค่าผ่าน parameter
- Service ใช้ Model ของตัวเองเป็นหลัก ถ้าต้องใช้ data ของ module อื่นให้เรียกผ่าน Service ของ module นั้น
- ไม่มี `catch` ที่ว่างเปล่าหรือ swallow exception
- งานตั้งเวลาหรือ batch ทำเป็น `spark` command ที่เรียก Service ตัวเดียวกับหน้าเว็บ
