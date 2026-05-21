# Custom fields

`src/Field/` ships a custom field layer that:

- Wraps EasyAdmin field types with a uniform builder API.
- Adds shared options (placeholder, max/min length, sanitization, layout defaults).
- Maps `displayIf(bool)` to the EasyAdmin `setPermission()` mechanism via `VirtualPermission`.
- Provides a single static factory (`FieldGenerator`) to create them.

These wrappers are used both from EasyAdmin CRUD controllers and from plain Symfony forms via `App\Form\FormGenerator` — see [forms](09-forms.md).

## `FieldTrait`

`src/Field/FieldTrait.php`. Trait composed by every wrapper. Internally uses EasyAdmin's `FieldTrait` and adds:

- `initField($innerField)` — copies the inner field DTO onto `$this->dto`, stamps the FQCN, and runs `applyDefaults()` (each subclass inserts its own customizations after `applyDefaultsTrait()`).
- Default column span: `DEFAULT_COLUMNS = 12`.
- Form-type / HTML attribute helpers: `isMapped`, `isRequired`, `isDisabled`, `isReadonly`, `setData`, `setPlaceholder`, `setMaxLength`, `setMinLength`, `isHtml`, `isSanitized`.
- `displayIf(bool)` — translates a visibility flag into `setPermission(VirtualPermission::allowed(...))`. Used everywhere in CRUD controllers to hide entire fields conditionally.

## `FieldGenerator`

`src/Field/FieldGenerator.php`. Static factories for every wrapper. Use these directly in CRUDs and forms instead of instantiating field classes:

```php
use App\Field\FieldGenerator;

FieldGenerator::text('name')->setLabel('entities.user.fields.name')->setColumns(2);
FieldGenerator::email('email')->isRequired();
FieldGenerator::role('role');                  // AssociationField + role template
FieldGenerator::userAvatar('avatar');          // MediaField with user-image conf
```

It also exposes the layout primitives delegating to `FormField`: `tab`, `panel` (alias for `fieldset`), `row`, `col`.

## Field catalog

All classes live under `App\Field`. Each one wraps the matching EasyAdmin type (or composes one) and exposes builder methods through `FieldTrait`.

| Wrapper                                                       | Wraps (EasyAdmin)                         | Notable behavior                                                                  |
| ------------------------------------------------------------- | ----------------------------------------- | --------------------------------------------------------------------------------- |
| `Field`                                                       | `Field`                                   | Generic delegate.                                                                 |
| `IdField`                                                     | `IdField`                                 | —                                                                                 |
| `TextField`                                                   | `TextField`                               | —                                                                                 |
| `MaskField`                                                   | `TextField`                               | IMask integration via `form-type-mask` asset.                                     |
| `HiddenField`                                                 | `TextField`                               | —                                                                                 |
| `SlugField`                                                   | `SlugField`                               | Slug plugin asset; `setTarget()` for source field; `setConfirmText()`.            |
| `TextareaField`                                               | `TextareaField`                           | —                                                                                 |
| `TextEditorField`                                             | `TextEditorField`                         | TinyMCE asset.                                                                    |
| `CodeEditorField`                                             | `CodeEditorField`                         | Ace; rich theme/language constants exposed.                                       |
| `ChoiceField`                                                 | `ChoiceField`                             | TomSelect-compatible.                                                             |
| `EnumField`                                                   | `ChoiceField`                             | Backed-enum aware (used with `App\Entity\Enum\UserGender`).                       |
| `BooleanField`                                                | `BooleanField`                            | `isSwitch`, `isHiddenOnTrue/False`, `isChecked`.                                  |
| `EmailField`                                                  | `EmailField`                              | —                                                                                 |
| `TelephoneField`                                              | `TelephoneField`                          | —                                                                                 |
| `UrlField`                                                    | `UrlField`                                | —                                                                                 |
| `DateField`                                                   | `DateField`                               | Flatpickr integration.                                                            |
| `DateTimeField`                                               | `DateTimeField`                           | Flatpickr.                                                                        |
| `TimeField`                                                   | `TimeField`                               | —                                                                                 |
| `DateMultipleField`                                           | composes `App\Form\Type\DateMultipleType` | Multi-date string input.                                                          |
| `DateTimeMultipleField`                                       | same                                      | Multi-datetime variant.                                                           |
| `TimezoneField`                                               | `TimezoneField`                           | —                                                                                 |
| `PasswordField`                                               | `TextField`                               | `isRepeated()` switches to `RepeatedType`; `renderSwitch()`, `renderGenerator()`. |
| `RepeatField`                                                 | composes `RepeatedType`                   | Generic repeat helper.                                                            |
| `IntegerField` / `FloatField` / `PercentField` / `MoneyField` | matching EA types                         | Numeric helpers.                                                                  |
| `ColorField`                                                  | `ColorField`                              | Spectrum vanilla picker.                                                          |
| `SignatureField`                                              | `TextField`                               | SignaturePad asset; show/undo/clear toggles.                                      |
| `MediaField`                                                  | `TextField` + Arkounay UxMedia            | Artgris `conf` keys (`public_all`, etc.); crop / zoom / size options.             |
| `FileField`                                                   | `TextField`                               | Plain file upload.                                                                |
| `ImageField`                                                  | `ImageField`                              | —                                                                                 |
| `ArrayField`                                                  | `ArrayField`                              | —                                                                                 |
| `CollectionField`                                             | `CollectionField`                         | Arkounay UxCollection compatible.                                                 |
| `AssociationField`                                            | `AssociationField`                        | TomSelect-friendly defaults; `setQueryBuilder`, `renderAsEmbeddedForm`.           |
| `UserField`                                                   | composes `AssociationField`               | Active/verified filters; card vs badge variant; avatar size; sublabel.            |
| `FormField`                                                   | `FormField`                               | Layout primitives — use `panel/fieldset/row/col/tab` static factories.            |

`FieldGenerator::role()` and `FieldGenerator::userAvatar()` are convenience factories that combine a wrapper with the right Twig template (`field/role.html.twig`, `field/userAvatar.html.twig`) and Artgris conf.

## Asset wiring per field

Most field wrappers auto-register a JavaScript entry point with `addAssetMapperEntries(Asset::new('form-type-...')->onlyOnForms())`. Frontend implementation lives under `assets/js/fields/` (see [assets](13-assets.md)). The EasyAdmin asset graph then loads each script only on screens where the field is rendered.

## Templates

Custom field templates are stored in `templates/field/`:

- `user.html.twig`, `userAvatar.html.twig`, `userIndexSelf.html.twig`
- `role.html.twig`, `roleIndexSelf.html.twig`
- `media.html.twig`, `file.html.twig`
- `dateMultiple.html.twig`, `datetimeMultiple.html.twig`
- `dateAgo.html.twig`, `age.html.twig`

EasyAdmin form theme overrides (used by `MediaField`, `SignatureField`, `PasswordField`...) live under `templates/bundles/EasyAdminBundle/crud/form_theme.html.twig`.
