# Primeros pasos

## Stack

Paquetes de backend y versiones resueltas: consulta `composer.lock` (restricciones en `composer.json`).

Librerías de frontend y puntos de entrada: consulta `importmap.php`.

## Bundles habilitados

`config/bundles.php` habilita, además de los bundles estándar de Symfony:

- `EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle`
- `Artgris\Bundle\FileManagerBundle\ArtgrisFileManagerBundle`
- `Arkounay\Bundle\UxCollectionBundle\ArkounayUxCollectionBundle`
- `Arkounay\Bundle\UxMediaBundle\ArkounayUxMediaBundle`
- `Knp\Bundle\TimeBundle\KnpTimeBundle`
- `Symfony\UX\Icons\UXIconsBundle`
- `Symfony\UX\Translator\UxTranslatorBundle`
- `SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle`
- `SymfonyCasts\Bundle\VerifyEmail\SymfonyCastsVerifyEmailBundle`

## Entorno

Los valores por defecto residen en `.env` (sobreescribibles con `.env.local`):

| Variable                  | Propósito                                                                            |
| ------------------------- | ------------------------------------------------------------------------------------ |
| `APP_ENV`                 | `dev` / `prod` / `test`                                                              |
| `APP_SECRET`              | Secreto de la aplicación Symfony                                                     |
| `DATABASE_URL`            | Conexión DBAL de Doctrine (MySQL/MariaDB/Postgres/SQLite)                            |
| `MAILER_DSN`              | DSN de transporte de Symfony Mailer (por defecto: `null://null`)                     |
| `MESSENGER_TRANSPORT_DSN` | Transporte asíncrono por defecto (Doctrine)                                          |
| `REQUIRED_SCHEME`         | Esquema requerido por el control de acceso de `security.yaml` (`http`/`https`)       |
| `LOCALES`                 | Lista permitida separada por barras, p. ej. `es\|en` (ver [i18n](15-i18n.md))        |
| `DEFAULT_URI`             | Usado para la generación de URLs en comandos CLI                                     |
| `APP_SHARE_DIR`           | Ruta del sistema de archivos usada como carpeta compartida (`var/share` por defecto) |

## Instalación e inicialización

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console app:create-users
```

`app:create-users` es idempotente: crea los roles `Superadmin / Admin / User` y las cuentas por defecto `superadmin@superadmin.com` (`superadmin`) y `admin@admin.com` (`admin`) si no existen. **Cambia las contraseñas generadas en cualquier entorno que no sea trivial.** Ver [console](16-console.md).

El panel de administración está en `/admin/{_locale}` (`DashboardController`); la portada pública en `/` (`PublicController`). El login está en `/login` (`AuthController`).

## Entidad de demo opcional

`docs/Demo/` incluye una entidad de ejemplo, un CRUD controller y un tipo de formulario. Actívalos con:

```bash
php bin/console app:demo
```

El comando intercambia archivos entre `docs/Demo/*.phps` y `src/`, luego ejecuta `doctrine:schema:update --force` y `app:update-permissions`.
