# Campos personalizados

`src/Field/` incluye una capa de campos personalizada que:

- Envuelve los tipos de campo de EasyAdmin con una API de construcción uniforme.
- Añade opciones compartidas (placeholder, longitud máxima/mínima, saneamiento, valores por defecto de layout).
- Mapea `displayIf(bool)` al mecanismo `setPermission()` de EasyAdmin mediante `VirtualPermission`.
- Proporciona una factoría estática única (`FieldGenerator`) para crearlos.

Estos wrappers se usan tanto desde los CRUD controllers de EasyAdmin como desde formularios Symfony normales a través de `App\Form\FormGenerator` — ver [forms](09-forms.md).

## `FieldTrait`

`src/Field/FieldTrait.php`. Trait que compone cada wrapper. Usa internamente el `FieldTrait` de EasyAdmin y añade:

- `initField($innerField)` — copia el DTO del campo interno sobre `$this->dto`, estampa el FQCN y ejecuta `applyDefaults()` (cada subclase inserta sus propias personalizaciones tras `applyDefaultsTrait()`).
- Span de columna por defecto: `DEFAULT_COLUMNS = 12`.
- Helpers de tipo de formulario / atributos HTML: `isMapped`, `isRequired`, `isDisabled`, `isReadonly`, `setData`, `setPlaceholder`, `setMaxLength`, `setMinLength`, `isHtml`, `isSanitized`.
- `displayIf(bool)` — traduce un flag de visibilidad en `setPermission(VirtualPermission::allowed(...))`. Se usa en todos los CRUD controllers para ocultar campos enteros de forma condicional.

## `FieldGenerator`

`src/Field/FieldGenerator.php`. Factorías estáticas para cada wrapper. Úsalas directamente en CRUDs y formularios en lugar de instanciar las clases de campo:

```php
use App\Field\FieldGenerator;

FieldGenerator::text('name')->setLabel('entities.user.fields.name')->setColumns(2);
FieldGenerator::email('email')->isRequired();
FieldGenerator::role('role');                  // AssociationField + plantilla de rol
FieldGenerator::userAvatar('avatar');          // MediaField con conf de imagen de usuario
```

También expone las primitivas de layout delegando en `FormField`: `tab`, `panel` (alias de `fieldset`), `row`, `col`.

## Catálogo de campos

Todas las clases residen bajo `App\Field`. Cada una envuelve el tipo EasyAdmin correspondiente (o compone uno) y expone métodos de construcción a través de `FieldTrait`.

| Wrapper                                                       | Envuelve (EasyAdmin)                      | Comportamiento destacado                                                          |
| ------------------------------------------------------------- | ----------------------------------------- | --------------------------------------------------------------------------------- |
| `Field`                                                       | `Field`                                   | Delegado genérico.                                                                |
| `IdField`                                                     | `IdField`                                 | —                                                                                 |
| `TextField`                                                   | `TextField`                               | —                                                                                 |
| `MaskField`                                                   | `TextField`                               | Integración IMask mediante el asset `form-type-mask`.                             |
| `HiddenField`                                                 | `TextField`                               | —                                                                                 |
| `SlugField`                                                   | `SlugField`                               | Asset del plugin slug; `setTarget()` para el campo origen; `setConfirmText()`.    |
| `TextareaField`                                               | `TextareaField`                           | —                                                                                 |
| `TextEditorField`                                             | `TextEditorField`                         | Asset TinyMCE.                                                                    |
| `CodeEditorField`                                             | `CodeEditorField`                         | Ace; constantes de tema/lenguaje expuestas.                                       |
| `ChoiceField`                                                 | `ChoiceField`                             | Compatible con TomSelect.                                                         |
| `EnumField`                                                   | `ChoiceField`                             | Compatible con backed-enum (usado con `App\Entity\Enum\UserGender`).              |
| `BooleanField`                                                | `BooleanField`                            | `isSwitch`, `isHiddenOnTrue/False`, `isChecked`.                                  |
| `EmailField`                                                  | `EmailField`                              | —                                                                                 |
| `TelephoneField`                                              | `TelephoneField`                          | —                                                                                 |
| `UrlField`                                                    | `UrlField`                                | —                                                                                 |
| `DateField`                                                   | `DateField`                               | Integración Flatpickr.                                                            |
| `DateTimeField`                                               | `DateTimeField`                           | Flatpickr.                                                                        |
| `TimeField`                                                   | `TimeField`                               | —                                                                                 |
| `DateMultipleField`                                           | compone `App\Form\Type\DateMultipleType`  | Entrada de múltiples fechas como cadena.                                          |
| `DateTimeMultipleField`                                       | igual                                     | Variante multifecha con hora.                                                     |
| `TimezoneField`                                               | `TimezoneField`                           | —                                                                                 |
| `PasswordField`                                               | `TextField`                               | `isRepeated()` cambia a `RepeatedType`; `renderSwitch()`, `renderGenerator()`.    |
| `RepeatField`                                                 | compone `RepeatedType`                    | Helper genérico de repetición.                                                    |
| `IntegerField` / `FloatField` / `PercentField` / `MoneyField` | tipos EA correspondientes                 | Helpers numéricos.                                                                |
| `ColorField`                                                  | `ColorField`                              | Selector Spectrum vanilla.                                                        |
| `SignatureField`                                              | `TextField`                               | Asset SignaturePad; toggles de mostrar/deshacer/limpiar.                          |
| `MediaField`                                                  | `TextField` + Arkounay UxMedia            | Claves `conf` de Artgris (`public_all`, etc.); opciones de recorte/zoom/tamaño.   |
| `FileField`                                                   | `TextField`                               | Subida de archivo simple.                                                         |
| `ImageField`                                                  | `ImageField`                              | —                                                                                 |
| `ArrayField`                                                  | `ArrayField`                              | —                                                                                 |
| `CollectionField`                                             | `CollectionField`                         | Compatible con Arkounay UxCollection.                                             |
| `AssociationField`                                            | `AssociationField`                        | Valores por defecto compatibles con TomSelect; `setQueryBuilder`, `renderAsEmbeddedForm`. |
| `UserField`                                                   | compone `AssociationField`                | Filtros de activo/verificado; variante tarjeta vs. insignia; tamaño de avatar; subetiqueta. |
| `FormField`                                                   | `FormField`                               | Primitivas de layout — usa las factorías estáticas `panel/fieldset/row/col/tab`.  |

`FieldGenerator::role()` y `FieldGenerator::userAvatar()` son factorías de conveniencia que combinan un wrapper con la plantilla Twig correcta (`field/role.html.twig`, `field/userAvatar.html.twig`) y la conf de Artgris.

## Registro de assets por campo

La mayoría de los wrappers de campo registran automáticamente un punto de entrada JavaScript con `addAssetMapperEntries(Asset::new('form-type-...')->onlyOnForms())`. La implementación frontend reside en `assets/js/fields/` (ver [assets](13-assets.md)). El grafo de assets de EasyAdmin carga entonces cada script únicamente en las pantallas donde se renderiza el campo.

## Plantillas

Las plantillas de campo personalizadas se almacenan en `templates/field/`:

- `user.html.twig`, `userAvatar.html.twig`, `userIndexSelf.html.twig`
- `role.html.twig`, `roleIndexSelf.html.twig`
- `media.html.twig`, `file.html.twig`
- `dateMultiple.html.twig`, `datetimeMultiple.html.twig`
- `dateAgo.html.twig`, `age.html.twig`

Las sobreescrituras del tema de formulario de EasyAdmin (usadas por `MediaField`, `SignatureField`, `PasswordField`...) residen en `templates/bundles/EasyAdminBundle/crud/form_theme.html.twig`.
