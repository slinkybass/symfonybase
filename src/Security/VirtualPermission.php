<?php

namespace App\Security;

/**
 * Virtual Symfony security attribute: never granted to users.
 * Pass to UI layers that call setPermission(); empty string means the element is allowed to show.
 */
final class VirtualPermission
{
    public const DENY = 'NOPERMISSION';

    public static function allowed(bool $isAllowed): string
    {
        return $isAllowed ? '' : self::DENY;
    }

    public static function isDenied(?string $attribute): bool
    {
        return $attribute === self::DENY;
    }
}
