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

if (! function_exists('vendor')) {
    /**
     * Return the base URL for third-party vendor assets in public/assets/vendor/
     *
     * @param string      $path   Relative path inside assets/vendor
     * @param string|null $scheme Optional URI scheme (e.g. 'http', 'https')
     */
    function vendor(string $path = '', ?string $scheme = null): string
    {
        $uri = 'assets/vendor' . ($path !== '' ? '/' . ltrim($path, '/') : '');

        return base_url($uri, $scheme);
    }
}

if (! function_exists('uuid')) {
    /**
     * Generate an RFC 9562 compliant UUID version 7 (time-ordered).
     *
     * @param bool $binary If true, returns 16-byte raw binary string instead of 36-char string.
     */
    function uuid(bool $binary = false): string
    {
        static $lastTime = 0;
        static $seq = 0;

        $time = (int) (microtime(true) * 1000);

        if ($time <= $lastTime) {
            $time = $lastTime;
            $seq = ($seq + 1) & 0x0FFF;
            if ($seq === 0) {
                $time = ++$lastTime;
            }
        } else {
            $lastTime = $time;
            $seq = random_int(0, 0x0FFF);
        }

        $bytes = random_bytes(8);

        $uuid = sprintf(
            '%08x-%04x-7%03x-%02x%02x-%02x%02x%02x%02x%02x%02x',
            ($time >> 16) & 0xFFFFFFFF,
            $time & 0xFFFF,
            $seq,
            (ord($bytes[0]) & 0x3F) | 0x80, // RFC 9562 variant
            ord($bytes[1]),
            ord($bytes[2]),
            ord($bytes[3]),
            ord($bytes[4]),
            ord($bytes[5]),
            ord($bytes[6]),
            ord($bytes[7]),
        );

        return $binary ? (string) hex2bin(str_replace('-', '', $uuid)) : $uuid;
    }
}
