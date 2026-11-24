<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class AuthInit extends Seeder
{
    public function run(): void
    {
        $users = auth()->getProvider();

        $email    = 'admin@homunculus.local';
        $username = 'superadmin';
        $password = 'Pass4Devs!2026#';

        // ตรวจสอบว่ามีผู้ใช้นี้อยู่แล้วหรือไม่ (Idempotent)
        $user = $users->findByCredentials(['email' => $email]);

        if ($user === null) {
            $user = new User([
                'username' => $username,
                'email'    => $email,
                'password' => $password,
            ]);

            $users->save($user);

            // ดึง User object ที่บันทึกแล้วพร้อม ID
            $user = $users->findById($users->getInsertID());

            // บันทึกสำเร็จ ให้ทำการ Activate บัญชี
            $user->activate();

            echo "Superadmin user created successfully: {$email}\n";
        } else {
            echo "Superadmin user already exists: {$email}\n";
        }

        // กำหนดกลุ่ม 'superadmin' หากยังไม่มี
        if (! $user->inGroup('superadmin')) {
            $user->addGroup('superadmin');
            echo "Assigned 'superadmin' group to user.\n";
        }
    }
}
