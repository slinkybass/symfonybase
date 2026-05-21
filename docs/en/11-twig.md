# Twig extensions and components

## Twig configuration

`config/packages/twig.yaml`:

- `file_name_pattern: '*.twig'`
- `form_themes: ['bootstrap_5_layout.html.twig']`
- Global: `locales` (from the `LOCALES` env var).

`config/packages/twig_component.yaml` registers `App\Twig\Components\` under the `components/` template directory and declares `components/` as the anonymous component directory.

## Extensions

All extensions live under `src/Twig/`.

### `AppConfigExtension`

Implements `GlobalsInterface` and exposes the cached `AppConfig` as the global `appConfig` Twig variable. See [configuration](03-configuration.md).

```twig
<title>{{ appConfig.appName }}</title>
{% if appConfig.enableCookies %} ... {% endif %}
```

### `RolePermissionsExtension`

Three Twig functions, all defaulting to the current security user:

```twig
{% if has_permission('media_upload') %} ... {% endif %}
{% if has_permission_crud('user') %} ... {% endif %}
{% if has_permission_crud_action('user', 'edit') %} ... {% endif %}
```

Non-`User` security tokens always evaluate as no permissions. See [permissions](05-permissions.md).

### `EnumExtension`

Filters and helpers for backed/unit enums:

| Filter / function                                        | Purpose                                      |
| -------------------------------------------------------- | -------------------------------------------- |
| `\| enum_name`                                           | Case name (`UserGender.male`)                |
| `\| enum_value`                                          | Backing value (or null for pure enums)       |
| `\| enum_label`                                          | Translated label via `TranslatableInterface` |
| `\| enum_choices(class)`                                 | Label-to-value map (forms)                   |
| `\| enum_from_value(class)` / `\| enum_from_name(class)` | Inverse lookups                              |
| `\| enum_is(other)`                                      | Compares against a case, name or value       |
| `enum_cases(class)`                                      | All cases                                    |
| `enum_count(class)`                                      | Number of cases                              |

Used in `RoleCrudController` and field templates that work with `UserGender`.

### `JsonDecodeExtension`

Adds the `json_decode` Twig filter that wraps PHP `json_decode()`.

### `HEXtoRGBExtension`

Adds the `hex_to_rgb` filter (`#RRGGBB` → `[R, G, B]`). Used by `templates/bundles/EasyAdminBundle/layout.html.twig` to derive Tabler's `--tblr-primary-rgb` CSS variable from `appConfig.appColor`.

## Live components

`src/Twig/Components/` (templates under `templates/components/`):

| Component (PHP) | Template                          | Purpose                                                                       |
| --------------- | --------------------------------- | ----------------------------------------------------------------------------- |
| `User`          | `components/User.html.twig`       | Card or badge variant; falls back to `user.anonymous` (EasyAdmin domain).     |
| `UserAvatar`    | `components/UserAvatar.html.twig` | Avatar image or initials with size variants and optional zoom.                |
| `Role`          | `components/Role.html.twig`       | Badge whose pastel color is derived deterministically from id + name.         |
| `Media`         | `components/Media.html.twig`      | Sized avatar-style image; image detection via extension or `data:image:` URI. |

`UserField` (`src/Field/UserField.php`) configures variants and avatar sizes that the `User` component then consumes through `field/user.html.twig`.

## Where to render what

- `templates/admin/` — admin-only pages (`home`, `media`).
- `templates/auth/` — login/register/reset templates.
- `templates/privacy/` — privacy and cookies pages.
- `templates/public/` — `home.html.twig` plus shared `layout/` and `includes/` partials.
- `templates/field/` — custom EasyAdmin / form field templates.
- `templates/mails/` — `base.html.twig` master template + `template.html.twig` rendered by `MailService` (see [email](14-email.md)).
- `templates/bundles/` — EasyAdmin / Artgris / UxMedia overrides (see [templates](12-templates.md)).
