<?php

namespace App\Security;

/**
 * Sentinel permission string for EasyAdmin (and similar) `setPermission()` calls.
 *
 * An empty string means the action or field is visible; `DENY` is never assigned to real roles,
 * so nothing matches and the UI stays hidden without coupling to a database permission row.
 */
final class VirtualPermission
{
    /** Impossible role name used only as a deny marker for `setPermission()`. */
    public const DENY = 'NOPERMISSION';

    private function __construct()
    {
    }

    /**
     * @return string `''` when the caller should expose the element, `DENY` when it must stay restricted
     */
    public static function allowed(bool $isAllowed): string
    {
        return $isAllowed ? '' : self::DENY;
    }

    /**
     * @param string|null $attribute value previously returned from `allowed()` or `null` when unset
     */
    public static function isDenied(?string $attribute): bool
    {
        return $attribute === self::DENY;
    }
}
