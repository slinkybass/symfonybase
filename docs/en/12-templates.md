# Templates layout

```
templates/
├── admin/                       # Admin pages rendered by AdminController / DashboardController
│   ├── home.html.twig
│   └── media.html.twig
├── auth/                        # Public auth flow templates
│   ├── register.html.twig
│   ├── reset_password_request.html.twig
│   └── reset_password.html.twig
├── components/                  # Live components (Twig templates)
│   ├── Media.html.twig
│   ├── Role.html.twig
│   ├── User.html.twig
│   └── UserAvatar.html.twig
├── field/                       # Custom EasyAdmin / form field templates
│   ├── age.html.twig
│   ├── dateAgo.html.twig
│   ├── dateMultiple.html.twig
│   ├── datetimeMultiple.html.twig
│   ├── file.html.twig
│   ├── media.html.twig
│   ├── role.html.twig
│   ├── roleIndexSelf.html.twig
│   ├── user.html.twig
│   ├── userAvatar.html.twig
│   └── userIndexSelf.html.twig
├── mails/                       # Email templates rendered by MailService
│   ├── base.html.twig
│   └── template.html.twig
├── privacy/                     # /privacy and /cookies pages
│   ├── cookies.html.twig
│   └── privacy.html.twig
├── public/                      # Public site
│   ├── home.html.twig
│   ├── includes/
│   └── layout/
└── bundles/                     # Bundle overrides
    ├── EasyAdminBundle/         # Layout, menu, flash messages, login, CRUD pages, fields, components
    ├── ArtgrisFileManagerBundle/
    └── ArkounayUxMediaBundle/
```

## Highlights

- `templates/bundles/EasyAdminBundle/layout.html.twig` overrides the EA layout. It pulls `appConfig` (logo, color, favicon, name, timezone) and computes the Tabler primary color CSS variables from `appConfig.appColor` using the `hex_to_rgb` Twig filter ([twig](11-twig.md)).
- `templates/bundles/EasyAdminBundle/menu.html.twig` and `flash_messages.html.twig` keep the EasyAdmin chrome but adapted to Tabler.
- `templates/bundles/EasyAdminBundle/page/login.html.twig` is the template rendered by `AuthController::login`.
- `templates/bundles/EasyAdminBundle/crud/` overrides `index`, `detail`, `new`, `edit`, `filters`, `paginator`, and the form theme. The `crud/field/` subfolder contains overrides for individual field types.
- `templates/bundles/EasyAdminBundle/components/` overrides `ActionMenu`, `Button`, and `Icon` Live Components used by EasyAdmin.
- `templates/bundles/EasyAdminBundle/label/empty.html.twig` is a blank label used by some custom field templates.
- `templates/bundles/ArtgrisFileManagerBundle/` and `templates/bundles/ArkounayUxMediaBundle/` adapt the file manager and the UX Media widget to the same look-and-feel.
- `templates/mails/base.html.twig` is the email skeleton extended by `template.html.twig`, which `MailService` renders for transactional emails (verification, password reset). See [email](14-email.md).
- `templates/admin/media.html.twig` is rendered by `AdminController::media` (`#[AdminRoute('/media')]`) and embeds the Artgris file manager.

## Field templates

The custom field renderers under `templates/field/` are referenced explicitly from PHP field wrappers via `setTemplatePath('field/foo.html.twig')`. Examples:

| File                                                               | Set by                                             |
| ------------------------------------------------------------------ | -------------------------------------------------- |
| `field/role.html.twig`                                             | `FieldGenerator::role()` / `UserField`             |
| `field/userAvatar.html.twig`                                       | `FieldGenerator::userAvatar()`                     |
| `field/user.html.twig`                                             | `App\Field\UserField`                              |
| `field/userIndexSelf.html.twig`, `field/roleIndexSelf.html.twig`   | `User`/`Role` CRUD controllers (own-row rendering) |
| `field/media.html.twig`                                            | `MediaField`, `SignatureField`                     |
| `field/dateMultiple.html.twig`, `field/datetimeMultiple.html.twig` | `DateMultipleField`, `DateTimeMultipleField`       |
| `field/age.html.twig`, `field/dateAgo.html.twig`                   | Custom field renderers used in CRUD listings       |
