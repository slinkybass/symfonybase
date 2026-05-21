# Configuration system

The base separates persisted configuration from the runtime DTO that controllers, subscribers, and Twig consume. The flow is:

```
Config (Doctrine entity) ──► ConfigService ──► AppConfig (DTO, cached) ──► everywhere
                                  ▲
                                  │ defaults + AssetMapper paths
```

## `App\Entity\Config`

Single configuration row stored in the `config` table (`src/Entity/Config.php`). Holds:

- App identity: `appName`, `appColor`, `appLogo`, `appFavicon`, `appDescription`, `appKeywords`, `appTimezone`, `senderEmail`.
- Public-area toggles: `enablePublic`, `enableResetPassword`, `enableRegister`, `enableCookies`.
- `roleDefaultRegister` — `ManyToOne` to `Role` used when a public visitor registers.
- Free HTML blocks: `privacyText`, `cookiesText`.

Logically a singleton; resolved through `ConfigRepository::filterFirst()`.

The settings UI is split between two CRUD controllers that target the same entity:

- `SettingsCrudController` — branding (name, color, logo, favicon, timezone, sender, description, keywords, privacy text, cookies text).
- `ConfigCrudController` — feature toggles (public area, register, reset password, cookies, default register role).

Both force a single existing record (denied `NEW` when one exists) and redirect after save to the detail view.

## `App\Model\AppConfig`

Mutable DTO assembled by `ConfigService` (`src/Model/AppConfig.php`). Exposes **only scalars** — including `roleDefaultRegisterId` (`?int`) instead of the `Role` entity — so the whole object can safely be cached.

Default values double as fallbacks when the `Config` row is missing.

## `App\Service\ConfigService`

Builds and caches the DTO under the `app_config` cache key (`src/Service/ConfigService.php`):

1. Defaults from `AppConfig`.
2. Overlays the `Config` row when present (each setter is null-safe).
3. Stamps `appLogo` / `appFavicon` from the AssetMapper public paths (`images/logo.png`, `images/favicon.png`); the DB values override them.
4. `roleDefaultRegisterId` is taken from `Config::getRoleDefaultRegister()->getId()`.

Always read configuration through `ConfigService::get()`; never reach for the `Config` entity directly from controllers, services, or Twig.

## Cache invalidation

`App\EventListener\ConfigCacheListener` (`src/EventListener/ConfigCacheListener.php`) is registered as a Doctrine entity listener for `Config` and clears the `app_config` cache item on `postPersist` and `postUpdate`. The cache key in the listener must stay in sync with `ConfigService::CACHE_KEY`.

## Reading from Twig

`App\Twig\AppConfigExtension` (`src/Twig/AppConfigExtension.php`) implements `GlobalsInterface` and exposes the resolved DTO as the global Twig variable `appConfig`. Templates such as the EasyAdmin layout override use it directly:

```twig
{# templates/bundles/EasyAdminBundle/layout.html.twig #}
{% block page_title %}{{ appConfig.appName }} - {{ block('content_title') }}{% endblock %}
```

## Reading from CRUD controllers

`AbstractCrudController::config()` returns the cached `AppConfig`. CRUD controllers in `src/Controller/Admin/Cruds/*` use it to branch on `enablePublic`, `enableRegister`, etc. (e.g. `RoleCrudController`, `AdminCrudController`, `SettingsCrudController`).

## Adding a new configuration field

1. Add the column to `App\Entity\Config` and generate a migration / run `doctrine:schema:update`.
2. Add the matching scalar property and default to `App\Model\AppConfig`.
3. Copy the DB value in `ConfigService::get()` (use `?? $config->field` to keep the default fallback).
4. Surface it in `SettingsCrudController` or `ConfigCrudController` as appropriate.
5. Localize the new field labels under `translations/messages.es.yaml` (`entities.settings.fields.*` or `entities.config.fields.*`).
