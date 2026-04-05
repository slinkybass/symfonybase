<?php

namespace App\Util;

/**
 * Generates cryptographically secure random passwords.
 */
final class PasswordGenerator
{
    private const UPPERCASE = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    private const LOWERCASE = 'abcdefghijkmnpqrstuvwxyz';
    private const NUMBERS = '23456789';
    private const SPECIALS = '!@#$%&_';

    /**
     * Generates a random password guaranteed to contain at least one uppercase letter,
     * one lowercase letter, one number and one special character.
     *
     * @param int $length the total length of the generated password (minimum 4)
     *
     * @throws \InvalidArgumentException if length is less than 4
     */
    public static function generate(int $length = 8): string
    {
        $all = self::UPPERCASE.self::LOWERCASE.self::NUMBERS.self::SPECIALS;

        $password = self::pick(self::SPECIALS);
        $password .= self::pick(self::LOWERCASE);
        $password .= self::pick(self::UPPERCASE);
        $password .= self::pick(self::NUMBERS);

        if ($length > 4) {
            $password .= self::pick($all, $length - 4);
        }

        $chars = str_split($password);
        shuffle($chars);

        return implode('', $chars);
    }

    private static function pick(string $str, int $count = 1): string
    {
        $result = '';
        $max = strlen($str) - 1;
        for ($i = 0; $i < $count; ++$i) {
            $result .= $str[random_int(0, $max)];
        }

        return $result;
    }
}
