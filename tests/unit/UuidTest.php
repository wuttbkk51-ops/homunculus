<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Unit test สำหรับฟังก์ชัน uuid_v4 ใน app/Common.php
 *
 * @internal
 */
final class UuidTest extends CIUnitTestCase
{
    /**
     * ทดสอบรูปแบบความถูกต้องของ UUIDv4 ตาม RFC 4122
     */
    public function testUuidV4Format(): void
    {
        $uuid = uuid_v4();

        // ตรวจสอบความยาว 36 ตัวอักษร
        $this->assertSame(36, strlen($uuid));

        // ตรวจสอบ format UUIDv4 (version 4 และ variant RFC 4122)
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';
        $this->assertMatchesRegularExpression($pattern, $uuid);
    }

    /**
     * ทดสอบว่าการสร้าง UUID แต่ละครั้งได้ค่าไม่ซ้ำกัน
     */
    public function testUuidV4Uniqueness(): void
    {
        $uuid1 = uuid_v4();
        $uuid2 = uuid_v4();

        $this->assertNotSame($uuid1, $uuid2);
    }
}
