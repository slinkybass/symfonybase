<?php

namespace App\Model;

/**
 * Mutable configuration DTO built by `ConfigService` (defaults + optional `Config` row + AssetMapper paths).
 *
 * Cached as a whole: use scalar fields only (e.g. `roleDefaultRegisterId`), never Doctrine entities or proxies.
 */
final class AppConfig
{
    public string $appName = 'Symfony Base';
    public string $appColor = '#22a6b3';
    public ?string $appLogo = null;
    public ?string $appFavicon = null;
    public string $appDescription = 'Created with Symfony';
    public string $appKeywords = 'symfony, application';
    public string $appTimezone = 'Europe/Madrid';
    public bool $enablePublic = false;
    public bool $enableResetPassword = false;
    public bool $enableRegister = false;
    public ?int $roleDefaultRegisterId = null;
    public bool $enableCookies = false;
    public string $senderEmail = 'israel@garaballu.com';
    public ?string $privacyText = null;
    public ?string $cookiesText = null;
}
