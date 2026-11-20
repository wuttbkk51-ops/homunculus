---
trigger: glob
globs: "tests/**"
description: กฎการเขียนและรัน test ด้วย PHPUnit ของ CI4
---

# Testing

- ใช้ PHPUnit ที่มากับ CI4 ไฟล์ test อยู่ใน `tests/` โครงสร้างสะท้อน `app/`
- test ที่แตะ database ใช้ `DatabaseTestTrait` และ migrate ใหม่ทุกครั้ง
- test ของ Service เรียก Service ตรงๆ ไม่ผ่าน HTTP
- test ของ route และ Filter ใช้ `FeatureTestTrait`
- การ login ใน test ให้ใช้ตามเอกสารของ Shield
- ตั้งชื่อ test ให้บอกพฤติกรรม เช่น `test_approve_fails_when_total_exceeds_limit`
- แก้ bug ต้องเขียน test ที่ fail ก่อนแก้ แล้วต้อง pass หลังแก้
- test รันบน SQLite ได้ แต่ถ้าแก้ Migration หรือ query ที่ซับซ้อน ให้ลองรันบน MySQL ด้วยก่อน merge
- ก่อนจบงานต้องรัน `composer test` และรายงานผลตามจริง ห้ามบอกว่า pass ถ้ายังไม่ได้รัน โดย commit โค้ดหลังจาก test ผ่านเรียบร้อยแล้วเท่านั้น
