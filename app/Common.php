<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('uuid_v4')) {
    /**
     * สร้าง UUIDv4 ตามมาตรฐาน RFC 4122
     */
    function uuid_v4(): string
    {
        $data    = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variant RFC 4122

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (! function_exists('uuid_v7')) {
    /**
     * สร้าง UUID v7 (เรียงตามเวลา) ใช้ตัวเดียวกันทั้งระบบตามกฎ 17
     */
    function uuid_v7(): string
    {
        // 48 บิตแรก = Unix time หน่วยมิลลิวินาที (6 byte), ที่เหลือสุ่มจาก CSPRNG
        $ms   = (int) (microtime(true) * 1000);
        $time = substr(pack('J', $ms), 2);
        $rand = random_bytes(10);

        // บิต version = 7 (ตำแหน่ง byte ที่ 7) และ variant = 10xx (ตำแหน่ง byte ที่ 9) ตาม RFC 9562
        $rand[0] = chr((ord($rand[0]) & 0x0f) | 0x70);
        $rand[2] = chr((ord($rand[2]) & 0x3f) | 0x80);

        $hex = bin2hex($time . $rand);

        return vsprintf('%s%s%s%s-%s%s-%s%s-%s%s-%s%s%s%s%s%s', str_split($hex, 2));
    }
}