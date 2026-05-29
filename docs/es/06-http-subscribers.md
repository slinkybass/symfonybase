# Suscriptores de eventos HTTP

Los eventos del kernel de Symfony gestionan el comportamiento transversal. Todos los suscriptores HTTP comprueban `$event->isMainRequest()` y residen en `src/EventSubscriber/`.

| Suscriptor               | Evento                       | Prioridad |
| ------------------------ | ---------------------------- | --------- |
| `LocaleSubscriber`       | `kernel.request`             | 40        |
| `MediaSubscriber`        | `kernel.request`             | 30        |
| `AccessSubscriber`       | `kernel.request`             | 7         |
| `InactiveUserSubscriber` | `kernel.request`             | 5         |
| `UserLoginSubscriber`    | `security.interactive_login` | 0         |

El entity listener de Doctrine `App\EventListener\ConfigCacheListener` se trata en [configuration](03-configuration.md).

## LocaleSubscriber

`src/EventSubscriber/LocaleSubscriber.php`.

- Resuelve el locale de la petición desde la sesión (clave `_locale`), inicializado por el atributo de petición `_locale` o la cadena de consulta.
- La lista de locales permitidos proviene de la variable de entorno `LOCALES` (separada por barras verticales; inyectada a través de `services.yaml`).
- Recurre a `kernel.default_locale` (`es`) cuando el locale almacenado no es válido.
- Requiere una sesión activa (no hace nada en caso contrario).

Ver [i18n](15-i18n.md) para la historia completa del locale.

## MediaSubscriber

`src/EventSubscriber/MediaSubscriber.php`.

- Solo se ejecuta para la ruta `file_manager` con un parámetro `?conf=` que corresponde a una de las claves `artgris_file_manager.conf` definidas en `config/packages/artgris_file_manager.yaml`.
- Crea en disco el directorio de subida configurado si no existe (`%kernel.project_dir%/public/media/...`).

La matriz de configuración de Artgris:

| Clave conf             | Directorio            | Tipo  |
| ---------------------- | --------------------- | ----- |
| `public_all`           | `public/media`        | any   |
| `public_images`        | `public/media`        | image |
| `public_config_images` | `public/media/config` | image |
| `public_user_images`   | `public/media/user`   | image |

## AccessSubscriber

`src/EventSubscriber/AccessSubscriber.php`. Decide si los usuarios anónimos o autenticados pueden acceder a ciertas rutas públicas, basándose en los flags de [`AppConfig`](03-configuration.md). Las rutas se agrupan mediante constantes privadas:

| Constante de grupo | Rutas                                                                     | Comportamiento                                                              |
| ------------------ | ------------------------------------------------------------------------- | --------------------------------------------------------------------------- |
| `PUBLIC_ROUTES`    | `home`                                                                    | Admins → `admin`; anónimos con `enablePublic = false` → `login`             |
| `LOGIN_ROUTES`     | `login`                                                                   | Usuarios autenticados → `home`                                              |
| `REGISTER_ROUTES`  | `register`, `verify`                                                      | Autenticados → `home`; en caso contrario requiere `enableRegister`          |
| `RESET_ROUTES`     | `reset_password_request`, `reset_password_request_sent`, `reset_password` | Autenticados → `home`; en caso contrario requiere `enableResetPassword`     |
| `PRIVACY_ROUTE`    | `privacy`                                                                 | Visible solo cuando `appConfig.privacyText` no está vacío                   |
| `COOKIES_ROUTE`    | `cookies`                                                                 | Visible solo cuando `appConfig.enableCookies` y `cookiesText` no está vacío |

Al añadir una ruta bajo alguna de estas políticas, registra su nombre en la constante correspondiente.

## InactiveUserSubscriber

`src/EventSubscriber/InactiveUserSubscriber.php`. Tras el login, una cuenta puede desactivarse a mitad de sesión. En cada petición principal (excepto la ruta `logout`) este suscriptor comprueba `User::isActive()` y ejecuta `Security::logout(false)` si devuelve `false`, sustituyendo la respuesta.

## UserLoginSubscriber

`src/EventSubscriber/UserLoginSubscriber.php`. Escucha `SecurityEvents::INTERACTIVE_LOGIN`. Lanza `DisabledException` (y añade un flash de error si existe una sesión) cuando el usuario recién autenticado:

- No está activo → `app.messages.userDeactivated`.
- No ha verificado el email → `app.messages.userUnverified`.

Symfony trata entonces el login como fallido.
