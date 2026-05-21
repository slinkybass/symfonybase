# Getting started

## Stack

Backend packages and their resolved versions: see `composer.lock` (constraints in `composer.json`).

Frontend libraries and entry points: see `importmap.php`.

## Bundles enabled

`config/bundles.php` enables, on top of stock Symfony bundles:

- `EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle`
- `Artgris\Bundle\FileManagerBundle\ArtgrisFileManagerBundle`
- `Arkounay\Bundle\UxCollectionBundle\ArkounayUxCollectionBundle`
- `Arkounay\Bundle\UxMediaBundle\ArkounayUxMediaBundle`
- `Knp\Bundle\TimeBundle\KnpTimeBundle`
- `Symfony\UX\Icons\UXIconsBundle`
- `Symfony\UX\Translator\UxTranslatorBundle`
- `SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle`
- `SymfonyCasts\Bundle\VerifyEmail\SymfonyCastsVerifyEmailBundle`

## Environment

Defaults live in `.env` (overridable with `.env.local`):

| Variable                  | Purpose                                                                 |
| ------------------------- | ----------------------------------------------------------------------- |
| `APP_ENV`                 | `dev` / `prod` / `test`                                                 |
| `APP_SECRET`              | Symfony app secret                                                      |
| `DATABASE_URL`            | Doctrine DBAL connection (MySQL/MariaDB/Postgres/SQLite)                |
| `MAILER_DSN`              | Symfony Mailer transport DSN (default: `null://null`)                   |
| `MESSENGER_TRANSPORT_DSN` | Default async transport (Doctrine)                                      |
| `REQUIRED_SCHEME`         | Required scheme used by `security.yaml` access control (`http`/`https`) |
| `LOCALES`                 | Pipe-separated allow-list, e.g. `es\|en` (see [i18n](15-i18n.md))          |
| `DEFAULT_URI`             | Used for URL generation in CLI commands                                 |
| `APP_SHARE_DIR`           | Filesystem path used as share folder (`var/share` by default)           |

## Install and bootstrap

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console app:create-users
```

`app:create-users` is idempotent: it seeds `Superadmin / Admin / User` roles and creates default `superadmin@superadmin.com` (`superadmin`) and `admin@admin.com` (`admin`) accounts when missing. **Replace the seeded passwords for any non-trivial environment.** See [console](16-console.md).

The dashboard lives at `/admin/{_locale}` (`DashboardController`); the public home is `/` (`PublicController`). Login is at `/login` (`AuthController`).

## Optional demo entity

`docs/Demo/` ships a sample entity, CRUD controller and form type. Toggle them with:

```bash
php bin/console app:demo
```

The command swaps files between `docs/Demo/*.phps` and `src/`, then runs `doctrine:schema:update --force` and `app:update-permissions`.
