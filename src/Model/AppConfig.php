<?php

namespace App\Model;

/**
 * Represents the application configuration resolved from the database and defaults.
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
