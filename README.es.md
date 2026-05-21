<div align="center">

# Symfony Base

**Un kit de inicio reutilizable con Symfony 7.4 + EasyAdmin 4 para construir aplicaciones orientadas a la administración.**

[![PHP](https://img.shields.io/badge/PHP-%5E8.4-777BB4?logo=php&logoColor=FFFFFF)](https://www.php.net)
[![Symfony](https://img.shields.io/badge/Symfony-7.4-000000?logo=symfony&logoColor=FFFFFF)](https://symfony.com)
[![Doctrine ORM](https://img.shields.io/badge/Doctrine_ORM-3.6-FC6A31?logo=doctrine&logoColor=FFFFFF)](https://www.doctrine-project.org)
[![EasyAdmin](https://img.shields.io/badge/EasyAdmin-4-blue)](https://github.com/EasyCorp/EasyAdminBundle)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue)](https://opensource.org/licenses/MIT)

</div>

> Una base pragmática y orientada a producción — **no es un framework ni una aplicación final**. Haz un fork, añade tus entidades de dominio y publica.

<div align="center">
	<img src="docs/images/dashboard.png" alt="dashboard" width="800"/>
</div>

---

## Índice de contenidos

- [Descripción general](#descripción-general)
- [Características](#características)
- [Pila tecnológica](#pila-tecnológica)
- [Cómo extiende Symfony / EasyAdmin](#cómo-extiende-symfony--easyadmin)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Primeros pasos](#primeros-pasos)
- [Despliegue en producción](#despliegue-en-producción)
- [Entidad de demostración opcional](#entidad-de-demostración-opcional)
- [Documentación](#documentación)
- [Convenciones](#convenciones)
- [Licencia](#licencia)

---

## Descripción general

**Symfony Base** es un punto de partida que agrupa el código repetitivo que toda aplicación Symfony orientada a la administración acaba reescribiendo: un árbol de permisos, un modelo de configuración, un catálogo de campos personalizados, un sistema de filtros para repositorios, flujos de autenticación y una interfaz de EasyAdmin con tema personalizado.

Todo está construido sobre componentes oficiales de Symfony y EasyAdmin, de modo que las actualizaciones siguen el ciclo de versiones oficial.

## Características

### Administración e interfaz

- Panel de EasyAdmin con tema basado en [Tabler](https://github.com/tabler/tabler) e interruptor de idioma.
- `AbstractCrudController` compartido con control de permisos por acción y ayudas de traducción.
- Catálogo de campos personalizados utilizables tanto en EasyAdmin como en formularios Symfony convencionales mediante una única fábrica `FieldGenerator`.
- Componentes Twig para `User`, `UserAvatar`, `Role` y `Media`.

<div align="center">
	<img src="docs/images/crud-list.png" alt="crud-list" width="800"/>
</div>

### Seguridad y control de acceso

- Inicio de sesión por formulario, registro, verificación por correo electrónico y restablecimiento de contraseña.
- Árbol de permisos automático (por CRUD y por acción), editable desde una interfaz jerárquica en el panel de roles.
- Suscriptores HTTP para políticas de acceso por ruta, resolución de idioma, cierre de sesión por inactividad y validación post-inicio de sesión.
- Suplantación de usuarios integrada mediante `switch_user` de Symfony.

<div align="center">
	<img src="docs/images/login.png" alt="login" width="800"/>
</div>
<div align="center">
	<img src="docs/images/permissions.png" alt="permissions" width="800"/>
</div>

### Frontend

- **Symfony Asset Mapper** (sin Webpack/Encore ni compilación con Node).
- Stimulus + Turbo, [Tabler](https://github.com/tabler/tabler)/[Bootstrap](https://github.com/twbs/bootstrap), [TomSelect](https://github.com/orchidjs/tom-select), [Flatpickr](https://github.com/flatpickr/flatpickr), [TinyMCE](https://github.com/tinymce/tinymce), [Ace](https://github.com/ajaxorg/ace), [SignaturePad](https://github.com/szimek/signature_pad), [Cropper.js](https://github.com/fengyuanchen/cropperjs), [noUiSlider](https://github.com/leongersen/noUiSlider), [Spectrum](https://github.com/asika32764/spectrum-vanilla), [IMask](https://github.com/uNmAnNeR/imaskjs), [SweetAlert2](https://github.com/sweetalert2/sweetalert2) — todos preconfigurados en `importmap.php`.

### i18n

- Idioma predeterminado `es`, con `en` listo de fábrica.
- Lista de idiomas permitidos gestionada por la variable de entorno `LOCALES`.
- Idioma persistente en sesión e interruptor de idioma en EasyAdmin.

## Pila tecnológica

| Capa              | Tecnología                                                                 |
| ----------------- | -------------------------------------------------------------------------- |
| **Entorno**       | PHP **^8.4**                                                               |
| **Framework**     | Symfony **7.4** (Framework, Security, Mailer, Form, Translator, UX)        |
| **Base de datos** | Doctrine ORM **3.6** · DBAL **4.4** · Migrations **3.9**                   |
| **Admin**         | EasyAdmin **4** · Arkounay UX Media / UX Collection · Artgris File Manager |
| **Auth**          | SymfonyCasts Reset Password & Verify Email                                 |
| **Frontend**      | Asset Mapper · Stimulus · Turbo · Tabler · Bootstrap 5                     |
| **Herramientas**  | PHPUnit 12 · PHP-CS-Fixer (`@Symfony`) · Symfony Maker                     |

Las versiones exactas están fijadas en [`composer.json`](composer.json), [`composer.lock`](composer.lock) e [`importmap.php`](importmap.php).

## Primeros pasos

### Instalación

```bash
git clone <este-repositorio> mi-app
cd mi-app
composer install
cp .env .env.local                                    # edita DATABASE_URL, MAILER_DSN, LOCALES, …
```

### Configuración para desarrollo

Para un entorno local rápido, genera el esquema directamente a partir de los mapeos de entidades:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force        # solo en desarrollo — usa migraciones en otros entornos
php bin/console app:create-users
php bin/console app:update-permissions
symfony serve -d                                      # o tu pila PHP local
```

`app:create-users` es idempotente y crea los roles y cuentas predeterminados:

| Correo electrónico          | Contraseña   | Rol               |
| --------------------------- | ------------ | ----------------- |
| `superadmin@superadmin.com` | `superadmin` | `ROLE_SUPERADMIN` |
| `admin@admin.com`           | `admin`      | `ROLE_ADMIN`      |

> ⚠️ Las credenciales predeterminadas son solo para el arranque local. **Cámbialas de inmediato en cualquier entorno compartido.**

## Despliegue en producción

En cualquier entorno que no sea local, utiliza las migraciones de Doctrine en lugar de `schema:update`:

```bash
composer install --no-dev --optimize-autoloader
php bin/console doctrine:migrations:diff              # genera una migración a partir de los cambios en las entidades
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console app:create-users                      # crea los roles solo en el primer despliegue
php bin/console app:update-permissions                # actualiza los permisos del superadmin
php bin/console cache:clear --env=prod
php bin/console asset-map:compile                     # compila la salida de Asset Mapper
```

Medidas de seguridad recomendadas para el entorno:

- Genera un `APP_SECRET` real y una `DATABASE_URL` no compartida.
- Configura un `MAILER_DSN` real (por defecto es `null://null`).
- Establece `REQUIRED_SCHEME=https` (aplicado mediante `security.yaml`).
- Limita `LOCALES` a los idiomas que realmente traduzas.
- Reemplaza los usuarios iniciales inmediatamente tras el arranque.

## Entidad de demostración opcional

Una entidad de ejemplo y su controlador CRUD están incluidos en `docs/Demo/` como archivos `.phps`. Actívalos o desactívalos con:

```bash
php bin/console app:demo
```

El comando intercambia los ficheros entre `docs/Demo/` y `src/`, actualiza el esquema y recalcula los permisos del superadmin.

## Documentación

La documentación técnica está [organizada por idioma](docs/README.md).

| Tema                                                                 | Página                                             |
| -------------------------------------------------------------------- | -------------------------------------------------- |
| Primeros pasos, entorno, inicialización por consola                  | [01-getting-started.md](docs/es/01-getting-started.md)   |
| Descripción general de la arquitectura                               | [02-architecture.md](docs/es/02-architecture.md)         |
| Sistema de configuración (Config + AppConfig + caché)                | [03-configuration.md](docs/es/03-configuration.md)       |
| Autenticación, registro, restablecimiento de contraseña              | [04-authentication.md](docs/es/04-authentication.md)     |
| Roles, árbol de permisos, permisos virtuales                         | [05-permissions.md](docs/es/05-permissions.md)           |
| Suscriptores HTTP                                                    | [06-http-subscribers.md](docs/es/06-http-subscribers.md) |
| Capa EasyAdmin (Dashboard + AbstractCrudController)                  | [07-easyadmin.md](docs/es/07-easyadmin.md)               |
| Campos personalizados y `FieldGenerator`                             | [08-fields.md](docs/es/08-fields.md)                     |
| Formularios (`FormGenerator`, formularios de autenticación públicos) | [09-forms.md](docs/es/09-forms.md)                       |
| Repositorios y filtros componibles                                   | [10-repositories.md](docs/es/10-repositories.md)         |
| Extensiones Twig y componentes en vivo                               | [11-twig.md](docs/es/11-twig.md)                         |
| Plantillas y sobreescrituras de bundles                              | [12-templates.md](docs/es/12-templates.md)               |
| Assets del frontend y Asset Mapper                                   | [13-assets.md](docs/es/13-assets.md)                     |
| Correo electrónico (`MailService`, plantillas transaccionales)       | [14-email.md](docs/es/14-email.md)                       |
| Internacionalización                                                 | [15-i18n.md](docs/es/15-i18n.md)                         |
| Comandos de consola                                                  | [16-console.md](docs/es/16-console.md)                   |

## Convenciones

- **Estilo** — Conjunto de reglas `@Symfony` de PHP-CS-Fixer con `yoda_style = false`. Consulta [`.php-cs-fixer.dist.php`](.php-cs-fixer.dist.php).
- **Idioma** — Predeterminado `es`. Consulta [`config/packages/translation.yaml`](config/packages/translation.yaml).
- **Tests** — PHPUnit 12; configuración en [`phpunit.dist.xml`](phpunit.dist.xml).

## Licencia

MIT (véase [`composer.json`](composer.json)). Adáptala a tus necesidades antes de publicar.
