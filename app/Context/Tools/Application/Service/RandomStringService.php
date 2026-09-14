<?php

declare(strict_types=1);

namespace App\Context\Tools\Application\Service;

final class RandomStringService
{
    private const string UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const string LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
    private const string DIGITS = '0123456789';
    private const string SYMBOLS = '!@#$%^&*()-_=+[]{}|;:,.<>?';

    public function generate(int $length, bool $uppercase, bool $lowercase, bool $digits, bool $symbols): string
    {
        $chars = '';
        if ($uppercase) {
            $chars .= self::UPPERCASE;
        }
        if ($lowercase) {
            $chars .= self::LOWERCASE;
        }
        if ($digits) {
            $chars .= self::DIGITS;
        }
        if ($symbols) {
            $chars .= self::SYMBOLS;
        }
        if ($chars === '') {
            $chars = self::UPPERCASE . self::LOWERCASE . self::DIGITS;
        }

        $result = '';

        for ($i = 0; $i < $length; ++$i) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $result;
    }
}
