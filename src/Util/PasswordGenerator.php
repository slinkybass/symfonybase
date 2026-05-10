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
    private const ALPHABET = self::UPPERCASE.self::LOWERCASE.self::NUMBERS.self::SPECIALS;

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
        $password = self::pick(self::SPECIALS);
        $password .= self::pick(self::LOWERCASE);
        $password .= self::pick(self::UPPERCASE);
        $password .= self::pick(self::NUMBERS);

        if ($length > 4) {
            $password .= self::pick(self::ALPHABET, $length - 4);
        }

        return self::shuffleString($password);
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

    private static function shuffleString(string $value): string
    {
        $chars = str_split($value);
        for ($i = count($chars) - 1; $i > 0; --$i) {
            $j = random_int(0, $i);
            if ($i !== $j) {
                [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
            }
        }

        return implode('', $chars);
    }
}
