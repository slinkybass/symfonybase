# Symfony Base — Documentation

Reusable starter built on **Symfony 7.4** and **EasyAdmin 4** that ships with extensions for configuration, permissions, custom fields, repository filters, and admin/public layouts.

Other locales: see the [documentation hub](../README.md).

## How this base extends Symfony / EasyAdmin

| Stock Symfony / EasyAdmin behavior   | What this base adds                                                                                 |
| ------------------------------------ | --------------------------------------------------------------------------------------------------- |
| `AbstractCrudController` (EasyAdmin) | `App\Controller\Admin\AbstractCrudController` (helpers, permission wiring...)                       |
| Field classes per type (EasyAdmin)   | `App\Field\*` wrappers + `FieldGenerator` factories with shared `FieldTrait`                        |
| Doctrine repositories                | `App\Repository\AbstractRepository` + composable `App\Repository\Filter\*` objects                  |
| Symfony Form types                   | `App\Form\FormGenerator` reuses `App\Field\*` outside of EasyAdmin                                  |
| `security.yaml` roles / `IsGranted`  | `App\Security\Permission` + `App\Security\Voter\PermissionVoter` over `App\Service\RolePermissions` |
| Per-route `IsGranted`                | `App\EventSubscriber\AccessSubscriber` driven by `App\Model\AppConfig`                              |
| Hard-coded settings                  | `App\Entity\Config` + cached `App\Model\AppConfig` via `App\Service\ConfigService`                  |
| Stock SymfonyCasts reset / verify    | Wired through `App\Controller\AuthController` + `App\Service\MailService`                           |

## Index

1. [Getting started](01-getting-started.md)
2. [Architecture overview](02-architecture.md)
3. [Configuration system](03-configuration.md)
4. [Authentication](04-authentication.md)
5. [Permissions](05-permissions.md)
6. [HTTP event subscribers](06-http-subscribers.md)
7. [EasyAdmin layer](07-easyadmin.md)
8. [Custom fields](08-fields.md)
9. [Forms](09-forms.md)
10. [Repositories and filters](10-repositories.md)
11. [Twig extensions and components](11-twig.md)
12. [Templates layout](12-templates.md)
13. [Frontend assets](13-assets.md)
14. [Email](14-email.md)
15. [Internationalization](15-i18n.md)
16. [Console commands](16-console.md)

## Conventions

- PHP-CS-Fixer ruleset is `@Symfony` with `yoda_style = false` (see `.php-cs-fixer.dist.php`).
- Translations default to Spanish (`es`) — see [i18n](15-i18n.md).
- All docs are kept short on purpose: each file targets one concept and links to the source files instead of duplicating implementation details.
