# Estructura de plantillas

```
templates/
├── admin/                       # Páginas de administración renderizadas por AdminController / DashboardController
│   ├── home.html.twig
│   └── media.html.twig
├── auth/                        # Plantillas públicas del flujo de autenticación
│   ├── register.html.twig
│   ├── reset_password_request.html.twig
│   └── reset_password.html.twig
├── components/                  # Componentes Live (plantillas Twig)
│   ├── Media.html.twig
│   ├── Role.html.twig
│   ├── User.html.twig
│   └── UserAvatar.html.twig
├── field/                       # Plantillas personalizadas de campos EasyAdmin / formulario
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
├── mails/                       # Plantillas de correo renderizadas por MailService
│   ├── base.html.twig
│   └── template.html.twig
├── privacy/                     # Páginas /privacy y /cookies
│   ├── cookies.html.twig
│   └── privacy.html.twig
├── public/                      # Sitio público
│   ├── home.html.twig
│   ├── includes/
│   └── layout/
└── bundles/                     # Sobrescrituras de bundles
    ├── EasyAdminBundle/         # Layout, menú, mensajes flash, login, páginas CRUD, campos, componentes
    ├── ArtgrisFileManagerBundle/
    └── ArkounayUxMediaBundle/
```

## Aspectos destacados

- `templates/bundles/EasyAdminBundle/layout.html.twig` sobrescribe el layout de EA. Obtiene `appConfig` (logo, color, favicon, nombre, zona horaria) y calcula las variables CSS del color primario de Tabler a partir de `appConfig.appColor` usando el filtro Twig `hex_to_rgb` ([twig](11-twig.md)).
- `templates/bundles/EasyAdminBundle/menu.html.twig` y `flash_messages.html.twig` mantienen el aspecto de EasyAdmin adaptado a Tabler.
- `templates/bundles/EasyAdminBundle/page/login.html.twig` es la plantilla renderizada por `AuthController::login`.
- `templates/bundles/EasyAdminBundle/crud/` sobrescribe `index`, `detail`, `new`, `edit`, `filters`, `paginator` y el tema del formulario. La subcarpeta `crud/field/` contiene sobrescrituras para tipos de campo individuales.
- `templates/bundles/EasyAdminBundle/components/` sobrescribe los Live Components `ActionMenu`, `Button` e `Icon` utilizados por EasyAdmin.
- `templates/bundles/EasyAdminBundle/label/empty.html.twig` es una etiqueta vacía usada por algunas plantillas de campos personalizados.
- `templates/bundles/ArtgrisFileManagerBundle/` y `templates/bundles/ArkounayUxMediaBundle/` adaptan el gestor de archivos y el widget UX Media al mismo aspecto visual.
- `templates/mails/base.html.twig` es el esqueleto de correo extendido por `template.html.twig`, que `MailService` renderiza para los correos transaccionales (verificación, restablecimiento de contraseña). Ver [email](14-email.md).
- `templates/admin/media.html.twig` es renderizado por `AdminController::media` (`#[AdminRoute('/media')]`) e integra el gestor de archivos de Artgris.

## Plantillas de campos

Los renderizadores de campos personalizados en `templates/field/` son referenciados explícitamente desde los wrappers PHP mediante `setTemplatePath('field/foo.html.twig')`. Ejemplos:

| Archivo                                                            | Definido por                                       |
| ------------------------------------------------------------------ | -------------------------------------------------- |
| `field/role.html.twig`                                             | `FieldGenerator::role()` / `UserField`             |
| `field/userAvatar.html.twig`                                       | `FieldGenerator::userAvatar()`                     |
| `field/user.html.twig`                                             | `App\Field\UserField`                              |
| `field/userIndexSelf.html.twig`, `field/roleIndexSelf.html.twig`   | Controladores CRUD de `User`/`Role` (renderizado de fila propia) |
| `field/media.html.twig`                                            | `MediaField`, `SignatureField`                     |
| `field/dateMultiple.html.twig`, `field/datetimeMultiple.html.twig` | `DateMultipleField`, `DateTimeMultipleField`       |
| `field/age.html.twig`, `field/dateAgo.html.twig`                   | Renderizadores de campos personalizados usados en listados CRUD |
