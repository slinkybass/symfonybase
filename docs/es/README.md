# Symfony Base — Documentación

Starter reutilizable construido sobre **Symfony 7.4** y **EasyAdmin 4** que incluye extensiones para configuración, permisos, campos personalizados, filtros de repositorio y layouts de administración y área pública.

Otros idiomas: ver el [hub de documentación](../README.md).

## Cómo esta base extiende Symfony / EasyAdmin

| Comportamiento base de Symfony / EasyAdmin | Lo que añade esta base                                                                    |
| ------------------------------------------ | ----------------------------------------------------------------------------------------- |
| `AbstractCrudController` (EasyAdmin)       | `App\Controller\Admin\AbstractCrudController` (helpers, integración de permisos, ...)     |
| Clases de campo por tipo (EasyAdmin)       | Wrappers `App\Field\*` + factorías `FieldGenerator` con `FieldTrait` compartido           |
| Repositorios de Doctrine                   | `App\Repository\AbstractRepository` + objetos `App\Repository\Filter\*` componibles       |
| Tipos de formulario de Symfony             | `App\Form\FormGenerator` reutiliza `App\Field\*` fuera de EasyAdmin                       |
| Roles de `security.yaml` / `IsGranted`     | `App\Service\RolePermissions` (árbol de permisos por CRUD + por acción)                   |
| `IsGranted` por ruta                       | `App\EventSubscriber\AccessSubscriber` guiado por `App\Model\AppConfig`                   |
| Ajustes codificados rígidamente            | `App\Entity\Config` + `App\Model\AppConfig` en caché mediante `App\Service\ConfigService` |
| Reset / verify estándar de SymfonyCasts    | Integrado a través de `App\Controller\AuthController` + `App\Service\MailService`         |

## Índice

1. [Primeros pasos](01-getting-started.md)
2. [Visión general de la arquitectura](02-architecture.md)
3. [Sistema de configuración](03-configuration.md)
4. [Autenticación](04-authentication.md)
5. [Permisos](05-permissions.md)
6. [Suscriptores de eventos HTTP](06-http-subscribers.md)
7. [Capa EasyAdmin](07-easyadmin.md)
8. [Campos personalizados](08-fields.md)
9. [Formularios](09-forms.md)
10. [Repositorios y filtros](10-repositories.md)
11. [Extensiones y componentes Twig](11-twig.md)
12. [Layout de plantillas](12-templates.md)
13. [Assets de frontend](13-assets.md)
14. [Email](14-email.md)
15. [Internacionalización](15-i18n.md)
16. [Comandos de consola](16-console.md)

## Convenciones

- El ruleset de PHP-CS-Fixer es `@Symfony` con `yoda_style = false` (ver `.php-cs-fixer.dist.php`).
- Las traducciones tienen el español (`es`) como idioma por defecto — ver [i18n](15-i18n.md).
- Toda la documentación es intencionadamente concisa: cada archivo aborda un concepto y enlaza a los archivos fuente en lugar de duplicar detalles de implementación.
