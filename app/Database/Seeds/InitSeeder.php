<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder กลางสำหรับเริ่มต้นระบบ (Init Seeder)
 *
 * ทำหน้าที่เป็น endpoint หลักในการเรียกทุก seeder ย่อย จบในที่เดียว
 */
class InitSeeder extends Seeder
{
    public function run(): void
    {
        // โหลดข้อมูลตัวอย่างเฉพาะสภาพแวดล้อม development เพื่อไม่ให้กระทบ production
        if (ENVIRONMENT !== 'production') {
            $this->call('DevMockSeeder');
        }
    }
}
