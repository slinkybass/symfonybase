# Formularios

`src/Form/` contiene los formularios orientados al público y un pequeño puente que permite reutilizar los wrappers de campo `App\Field\*` (diseñados para EasyAdmin) en formularios Symfony normales.

## `App\Form\FormGenerator`

`src/Form/FormGenerator.php`. Mapea un array de `App\Field\*` (o cualquier cosa que exponga `getAsDto()`) sobre un `FormBuilderInterface`:

```php
$this->formGenerator->getFormBuilder($builder, [
    FieldGenerator::email('email')->setLabel('entities.user.fields.email'),
    FieldGenerator::password('plainPassword')->isRepeated()->isMapped(false),
], User::class);
```

Para cada campo:

1. Lee las opciones de tipo de formulario y la etiqueta desde el DTO envuelto.
2. Cuando se proporciona `$entityClass`, infiere `required` y (para backed enums) la opción de formulario `class` a partir de los metadatos de Doctrine (`hasField` + `getFieldMapping`).
3. Añade `data-ea-widget="ea-autocomplete"` a los widgets de selección estilo TomSelect para que el JS de autocompletado de EasyAdmin los detecte fuera de contextos `EasyAdmin`.

Pasa `submitField: true` para añadir un botón `SubmitType` de guardado.

## Formularios públicos

Todos los formularios públicos son clases `AbstractType` sencillas construidas sobre `FormGenerator`:

| Formulario                          | Usado por                              | Campos principales                                                                       |
| ----------------------------------- | -------------------------------------- | ---------------------------------------------------------------------------------------- |
| `App\Form\RegistrationForm`         | `AuthController::register`             | name, lastname, email, phone, birthdate, gender, `plainPassword` repetido, `acceptTerms` |
| `App\Form\ChangePasswordForm`       | `AuthController::resetPassword`        | `plainPassword` no mapeado y repetido                                                    |
| `App\Form\ResetPasswordRequestForm` | `AuthController::resetPasswordRequest` | email                                                                                    |

`RegistrationForm` alterna la etiqueta de los términos entre `public.register.acceptTerms` y `public.register.acceptTermsUrl` según si existe `appConfig.privacyText`; el placeholder de la URL apunta a la ruta `privacy`. Tanto `acceptTerms` como `plainPassword` son `isMapped(false)`.

`ChangePasswordForm` usa un `plainPassword` no mapeado y repetido; el controlador lo hashea y lo asigna al usuario manualmente.

## Tipos de formulario personalizados

`src/Form/Type/`:

- `DateMultipleType` — campo de texto cuyo valor de modelo es `string[]`. `transform()` une los valores con `", "`, `reverseTransform()` divide/recorta y descarta vacíos, devolviendo `null` para arrays vacíos. Usado por `App\Field\DateMultipleField` y `App\Field\DateTimeMultipleField`.

## Cuándo usar cada opción

- Dentro de un CRUD de EasyAdmin: devuelve factorías `App\Field\*` desde `configureFields()` — son wrappers de campo.
- Fuera de EasyAdmin (páginas de autenticación, formularios personalizados): construye un `AbstractType` que delega en `FormGenerator` con las mismas factorías. El `App\Field\FieldTrait` compartido mantiene idénticas las opciones de tipo de formulario en ambos contextos.
