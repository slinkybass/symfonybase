# Extensiones y componentes Twig

## Configuración de Twig

`config/packages/twig.yaml`:

- `file_name_pattern: '*.twig'`
- `form_themes: ['bootstrap_5_layout.html.twig']`
- Global: `locales` (desde la variable de entorno `LOCALES`).

`config/packages/twig_component.yaml` registra `App\Twig\Components\` bajo el directorio de plantillas `components/` y declara `components/` como directorio de componentes anónimos.

## Extensiones

Todas las extensiones se encuentran en `src/Twig/`.

### `AppConfigExtension`

Implementa `GlobalsInterface` y expone el `AppConfig` cacheado como la variable Twig global `appConfig`. Ver [configuration](03-configuration.md).

```twig
<title>{{ appConfig.appName }}</title>
{% if appConfig.enableCookies %} ... {% endif %}
```

### `RolePermissionsExtension`

Tres funciones Twig, todas tomando por defecto el usuario de seguridad actual:

```twig
{% if has_permission('media_upload') %} ... {% endif %}
{% if has_permission_crud('user') %} ... {% endif %}
{% if has_permission_crud_action('user', 'edit') %} ... {% endif %}
```

Los tokens de seguridad que no sean de tipo `User` siempre se evalúan sin permisos. Ver [permissions](05-permissions.md).

### `EnumExtension`

Filtros y helpers para enums con o sin valor de respaldo:

| Filtro / función                                         | Propósito                                              |
| -------------------------------------------------------- | ------------------------------------------------------ |
| `\| enum_name`                                           | Nombre del caso (`UserGender.male`)                    |
| `\| enum_value`                                          | Valor de respaldo (o null para enums puros)            |
| `\| enum_label`                                          | Etiqueta traducida mediante `TranslatableInterface`    |
| `\| enum_choices(class)`                                 | Mapa etiqueta→valor (formularios)                      |
| `\| enum_from_value(class)` / `\| enum_from_name(class)` | Búsquedas inversas                                     |
| `\| enum_is(other)`                                      | Compara contra un caso, nombre o valor                 |
| `enum_cases(class)`                                      | Todos los casos                                        |
| `enum_count(class)`                                      | Número de casos                                        |

Usado en `RoleCrudController` y en las plantillas de campos que trabajan con `UserGender`.

### `JsonDecodeExtension`

Añade el filtro Twig `json_decode` que envuelve la función PHP `json_decode()`.

### `HEXtoRGBExtension`

Añade el filtro `hex_to_rgb` (`#RRGGBB` → `[R, G, B]`). Usado por `templates/bundles/EasyAdminBundle/layout.html.twig` para derivar la variable CSS `--tblr-primary-rgb` de Tabler a partir de `appConfig.appColor`.

## Componentes Live

`src/Twig/Components/` (plantillas en `templates/components/`):

| Componente (PHP) | Plantilla                         | Propósito                                                                                    |
| ---------------- | --------------------------------- | -------------------------------------------------------------------------------------------- |
| `User`           | `components/User.html.twig`       | Variante de tarjeta o badge; recurre a `user.anonymous` (dominio EasyAdmin).                 |
| `UserAvatar`     | `components/UserAvatar.html.twig` | Imagen de avatar o iniciales con variantes de tamaño y zoom opcional.                        |
| `Role`           | `components/Role.html.twig`       | Badge cuyo color pastel se deriva de forma determinista a partir del id y el nombre.         |
| `Media`          | `components/Media.html.twig`      | Imagen estilo avatar con tamaño; detección de imagen por extensión o URI `data:image:`.      |

`UserField` (`src/Field/UserField.php`) configura las variantes y tamaños de avatar que el componente `User` consume a través de `field/user.html.twig`.

## Dónde renderizar cada cosa

- `templates/admin/` — páginas exclusivas de administración (`home`, `media`).
- `templates/auth/` — plantillas de login, registro y restablecimiento de contraseña.
- `templates/privacy/` — páginas de privacidad y cookies.
- `templates/public/` — `home.html.twig` más los parciales compartidos de `layout/` e `includes/`.
- `templates/field/` — plantillas de campos personalizados de EasyAdmin / formulario.
- `templates/mails/` — plantilla maestra `base.html.twig` + `template.html.twig` renderizado por `MailService` (ver [email](14-email.md)).
- `templates/bundles/` — sobrescrituras de EasyAdmin / Artgris / UxMedia (ver [templates](12-templates.md)).
