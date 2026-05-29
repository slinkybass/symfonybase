# Architecture overview

The codebase follows the standard Symfony Flex layout. This page maps the top-level folders to the responsibilities they actually carry in this base.

## `src/`

```
src/
├── Command/                     # Console: app:create-users, app:update-permissions, app:demo
├── Controller/
│   ├── Admin/                   # EasyAdmin layer
│   │   ├── Cruds/               # One *CrudController per entity exposed in EA
│   │   ├── AbstractCrudController.php
│   │   └── DashboardController.php
│   ├── AdminController.php      # Non-CRUD admin routes (e.g. /media)
│   ├── AuthController.php       # login / register / verify / reset password
│   ├── PrivacyController.php    # /privacy and /cookies pages
│   └── PublicController.php     # / (public home)
├── Entity/                      # User, Role, Config, ResetPasswordRequest + Enum/UserGender
├── EventListener/               # Doctrine entity listeners (config cache invalidation)
├── EventSubscriber/             # Locale, Media, Access, InactiveUser, UserLogin
├── Field/                       # EasyAdmin-style field wrappers + FieldGenerator factory
├── Form/                        # FormGenerator + auth/registration/password forms + Form\Type
├── Model/                       # AppConfig DTO (cached configuration)
├── Repository/                  # AbstractRepository + entity repositories
│   └── Filter/                  # Filter system: AbstractFilter + per-entity filter classes
├── Security/                    # AdminUserTrait + Permission helper + PermissionVoter + VirtualPermission sentinel
├── Service/                     # ConfigService, MailService, RolePermissions
├── Twig/
│   ├── Components/              # Live components (User, Role, UserAvatar, Media)
│   └── *Extension.php           # AppConfig global, RolePermissions, Enum, JSON, HEX→RGB
├── Util/                        # PasswordGenerator
└── Kernel.php
```

Each subfolder has its own dedicated documentation page (see [README](README.md)).

## `config/`

`config/bundles.php`, `config/services.yaml` and `config/packages/*.yaml` follow Symfony defaults. Notable customizations:

- `services.yaml` declares the `locales` parameter and explicit arguments for `LocaleSubscriber` / `MediaSubscriber`.
- `security.yaml` declares one `main` firewall, `app_user_provider` based on `User.email`, and `switch_user: { role: ROLE_ADMIN }`.
- `doctrine.yaml` sets the `underscore_number_aware` naming strategy and maps `App\Entity` from attributes.
- `translation.yaml` sets `default_locale: es` and `fallbacks: [es]`.
- `artgris_file_manager.yaml` declares four file-manager configs: `public_all`, `public_images`, `public_config_images`, `public_user_images`.
- `reset_password.yaml` wires `ResetPasswordRequestRepository`.
- `twig_component.yaml` registers `App\Twig\Components\` under `components/`.

## `assets/`, `templates/`, `translations/`

- [Frontend assets](13-assets.md) — Asset Mapper layout: entrypoints, page modules, field plugins, Stimulus controllers.
- [Templates](12-templates.md) — Twig layout, EasyAdmin / Artgris / UxMedia bundle overrides, mail templates and Live Components.
- [i18n](15-i18n.md) — translations under `translations/messages.es.yaml`, `EasyAdminBundle.es.yaml`, `ArkounayUxMediaBundle.es.yaml`, `validators/`.
