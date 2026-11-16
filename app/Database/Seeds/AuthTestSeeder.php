<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder สำหรับสร้างข้อมูลทดสอบ (ส่งต่อการทำงานไปยัง DatabaseSeeder)
 */
class AuthTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DatabaseSeeder::class);
    }
}
