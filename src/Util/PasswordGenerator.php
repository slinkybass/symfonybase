<?php

namespace App\Util;

/**
 * Builds human-readable passwords with cryptographically secure randomness per character.
 *
 * The character sets omit ambiguous glyphs (e.g. O/0, I/l/1). The first four slots always use
 * one symbol, one lowercase, one uppercase, and one digit; additional length draws from the full
 * pool. Requested lengths below four still yield four characters from those mandatory pools.
 */
final class PasswordGenerator
{
    private const UPPERCASE = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    private const LOWERCASE = 'abcdefghijkmnpqrstuvwxyz';
    private const NUMBERS = '23456789';
    private const SPECIALS = '!@#$%&_';
    private const ALPHABET = self::UPPERCASE.self::LOWERCASE.self::NUMBERS.self::SPECIALS;

    /**
     * @param int $length Desired total length; result length is max(4, $length)
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

    /**
     * @param non-empty-string $str
     */
    private static function pick(string $str, int $count = 1): string
    {
        $result = '';
        $max = strlen($str) - 1;
        for ($i = 0; $i < $count; ++$i) {
            $result .= $str[random_int(0, $max)];
        }

        return $result;
    }

    /** Fisher-Yates shuffle of single-byte characters in $value. */
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
