# Visión general de la arquitectura

El código sigue la estructura estándar de Symfony Flex. Esta página relaciona las carpetas de primer nivel con las responsabilidades que desempeñan en esta base.

## `src/`

```
src/
├── Command/                     # Consola: app:create-users, app:update-permissions, app:demo
├── Controller/
│   ├── Admin/                   # Capa EasyAdmin
│   │   ├── Cruds/               # Un *CrudController por entidad expuesta en EA
│   │   ├── AbstractCrudController.php
│   │   └── DashboardController.php
│   ├── AdminController.php      # Rutas de administración no-CRUD (p. ej. /media)
│   ├── AuthController.php       # login / registro / verificación / recuperación de contraseña
│   ├── PrivacyController.php    # Páginas /privacy y /cookies
│   └── PublicController.php     # / (portada pública)
├── Entity/                      # User, Role, Config, ResetPasswordRequest + Enum/UserGender
├── EventListener/               # Entity listeners de Doctrine (invalidación de caché de config)
├── EventSubscriber/             # Locale, Media, Access, InactiveUser, UserLogin
├── Field/                       # Wrappers de campos estilo EasyAdmin + factoría FieldGenerator
├── Form/                        # FormGenerator + formularios de auth/registro/contraseña + Form\Type
├── Model/                       # DTO AppConfig (configuración en caché)
├── Repository/                  # AbstractRepository + repositorios por entidad
│   └── Filter/                  # Sistema de filtros: AbstractFilter + clases de filtro por entidad
├── Security/                    # AdminUserTrait + helper Permission + PermissionVoter + centinela VirtualPermission
├── Service/                     # ConfigService, MailService, RolePermissions
├── Twig/
│   ├── Components/              # Componentes Live (User, Role, UserAvatar, Media)
│   └── *Extension.php           # Global AppConfig, RolePermissions, Enum, JSON, HEX→RGB
├── Util/                        # PasswordGenerator
└── Kernel.php
```

Cada subcarpeta tiene su propia página de documentación (ver [README](README.md)).

## `config/`

`config/bundles.php`, `config/services.yaml` y `config/packages/*.yaml` siguen los valores por defecto de Symfony. Personalizaciones destacadas:

- `services.yaml` declara el parámetro `locales` y los argumentos explícitos para `LocaleSubscriber` / `MediaSubscriber`.
- `security.yaml` declara un único firewall `main`, el `app_user_provider` basado en `User.email` y `switch_user: { role: ROLE_ADMIN }`.
- `doctrine.yaml` establece la estrategia de nomenclatura `underscore_number_aware` y mapea `App\Entity` desde atributos.
- `translation.yaml` define `default_locale: es` y `fallbacks: [es]`.
- `artgris_file_manager.yaml` declara cuatro configuraciones de gestor de archivos: `public_all`, `public_images`, `public_config_images`, `public_user_images`.
- `reset_password.yaml` conecta el `ResetPasswordRequestRepository`.
- `twig_component.yaml` registra `App\Twig\Components\` bajo `components/`.

## `assets/`, `templates/`, `translations/`

- [Assets de frontend](13-assets.md) — Estructura de Asset Mapper: entrypoints, módulos de página, plugins de campo, controladores Stimulus.
- [Plantillas](12-templates.md) — Layout de Twig, sobreescrituras de los bundles EasyAdmin / Artgris / UxMedia, plantillas de correo y Live Components.
- [i18n](15-i18n.md) — Traducciones en `translations/messages.es.yaml`, `EasyAdminBundle.es.yaml`, `ArkounayUxMediaBundle.es.yaml`, `validators/`.
