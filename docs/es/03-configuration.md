# Sistema de configuración

La base separa la configuración persistida del DTO en tiempo de ejecución que consumen controladores, suscriptores y Twig. El flujo es:

```
Config (entidad Doctrine) ──► ConfigService ──► AppConfig (DTO, en caché) ──► en todas partes
                                   ▲
                                   │ valores por defecto + rutas de AssetMapper
```

## `App\Entity\Config`

Fila de configuración única almacenada en la tabla `config` (`src/Entity/Config.php`). Contiene:

- Identidad de la app: `appName`, `appColor`, `appLogo`, `appFavicon`, `appDescription`, `appKeywords`, `appTimezone`, `senderEmail`.
- Toggles del área pública: `enablePublic`, `enableResetPassword`, `enableRegister`, `enableCookies`.
- `roleDefaultRegister` — `ManyToOne` hacia `Role`, utilizado cuando un visitante público se registra.
- Bloques HTML libres: `privacyText`, `cookiesText`.

Lógicamente es un singleton; se resuelve a través de `ConfigRepository::filterFirst()`.

La interfaz de ajustes se divide entre dos CRUD controllers que apuntan a la misma entidad:

- `SettingsCrudController` — identidad visual (nombre, color, logo, favicon, zona horaria, remitente, descripción, palabras clave, texto de privacidad, texto de cookies).
- `ConfigCrudController` — toggles de funcionalidades (área pública, registro, recuperación de contraseña, cookies, rol por defecto en el registro).

Ambos fuerzan un único registro existente (se deniega `NEW` si ya existe uno) y redirigen tras guardar a la vista de detalle.

## `App\Model\AppConfig`

DTO mutable ensamblado por `ConfigService` (`src/Model/AppConfig.php`). Expone **solo escalares** —incluyendo `roleDefaultRegisterId` (`?int`) en lugar de la entidad `Role`— para que el objeto completo pueda almacenarse en caché de forma segura.

Los valores por defecto actúan como fallback cuando la fila `Config` no existe.

## `App\Service\ConfigService`

Construye y almacena en caché el DTO bajo la clave `app_config` (`src/Service/ConfigService.php`):

1. Valores por defecto de `AppConfig`.
2. Superpone la fila `Config` cuando existe (cada setter es null-safe).
3. Asigna `appLogo` / `appFavicon` desde las rutas públicas de AssetMapper (`images/logo.png`, `images/favicon.png`); los valores de la BD los sobreescriben.
4. `roleDefaultRegisterId` se obtiene de `Config::getRoleDefaultRegister()->getId()`.

Accede siempre a la configuración a través de `ConfigService::get()`; nunca accedas directamente a la entidad `Config` desde controladores, servicios o Twig.

## Invalidación de caché

`App\EventListener\ConfigCacheListener` (`src/EventListener/ConfigCacheListener.php`) está registrado como entity listener de Doctrine para `Config` y limpia el ítem de caché `app_config` en `postPersist` y `postUpdate`. La clave de caché en el listener debe mantenerse sincronizada con `ConfigService::CACHE_KEY`.

## Lectura desde Twig

`App\Twig\AppConfigExtension` (`src/Twig/AppConfigExtension.php`) implementa `GlobalsInterface` y expone el DTO resuelto como la variable global de Twig `appConfig`. Las plantillas, como la sobreescritura del layout de EasyAdmin, la usan directamente:

```twig
{# templates/bundles/EasyAdminBundle/layout.html.twig #}
{% block page_title %}{{ appConfig.appName }} - {{ block('content_title') }}{% endblock %}
```

## Lectura desde CRUD controllers

`AbstractCrudController::config()` devuelve el `AppConfig` en caché. Los CRUD controllers en `src/Controller/Admin/Cruds/*` lo usan para ramificar según `enablePublic`, `enableRegister`, etc. (p. ej. `RoleCrudController`, `AdminCrudController`, `SettingsCrudController`).

## Añadir un nuevo campo de configuración

1. Añade la columna a `App\Entity\Config` y genera una migración / ejecuta `doctrine:schema:update`.
2. Añade la propiedad escalar correspondiente y su valor por defecto en `App\Model\AppConfig`.
3. Copia el valor de la BD en `ConfigService::get()` (usa `?? $config->field` para mantener el fallback por defecto).
4. Muéstralo en `SettingsCrudController` o `ConfigCrudController` según corresponda.
5. Localiza las etiquetas del nuevo campo en `translations/messages.es.yaml` (`entities.settings.fields.*` o `entities.config.fields.*`).
