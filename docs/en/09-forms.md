# Forms

`src/Form/` keeps the public-facing forms and a small bridge that lets `App\Field\*` field wrappers (designed for EasyAdmin) be reused in plain Symfony forms.

## `App\Form\FormGenerator`

`src/Form/FormGenerator.php`. Maps an array of `App\Field\*` (or anything exposing `getAsDto()`) onto a `FormBuilderInterface`:

```php
$this->formGenerator->getFormBuilder($builder, [
    FieldGenerator::email('email')->setLabel('entities.user.fields.email'),
    FieldGenerator::password('plainPassword')->isRepeated()->isMapped(false),
], User::class);
```

For each field:

1. Reads form-type options + label from the wrapped DTO.
2. When `$entityClass` is provided, infers `required` and (for backed enums) the `class` form option from Doctrine metadata (`hasField` + `getFieldMapping`).
3. Adds `data-ea-widget="ea-autocomplete"` to TomSelect-style choice widgets so the EasyAdmin autocomplete JS picks them up outside of `EasyAdmin` contexts.

Pass `submitField: true` to append a `save` `SubmitType` button.

## Public forms

All public forms are simple `AbstractType` classes built on top of `FormGenerator`:

| Form                                | Used by                                | Key fields                                                                               |
| ----------------------------------- | -------------------------------------- | ---------------------------------------------------------------------------------------- |
| `App\Form\RegistrationForm`         | `AuthController::register`             | name, lastname, email, phone, birthdate, gender, repeated `plainPassword`, `acceptTerms` |
| `App\Form\ChangePasswordForm`       | `AuthController::resetPassword`        | repeated unmapped `plainPassword`                                                        |
| `App\Form\ResetPasswordRequestForm` | `AuthController::resetPasswordRequest` | email                                                                                    |

`RegistrationForm` swaps the terms label between `public.register.acceptTerms` and `public.register.acceptTermsUrl` based on whether `appConfig.privacyText` exists; the URL placeholder points to the `privacy` route. `acceptTerms` and `plainPassword` are both `isMapped(false)`.

`ChangePasswordForm` uses a repeated unmapped `plainPassword`; the controller hashes and assigns it to the user manually.

## Custom form types

`src/Form/Type/`:

- `DateMultipleType` — text input whose model value is `string[]`. `transform()` joins with `", "`, `reverseTransform()` splits/trims and drops empties, returning `null` for empty arrays. Used by `App\Field\DateMultipleField` and `App\Field\DateTimeMultipleField`.

## Where to use what

- Inside an EasyAdmin CRUD: yield `App\Field\*` factories from `configureFields()` — they are field wrappers.
- Outside EasyAdmin (auth pages, custom forms): build an `AbstractType` that delegates to `FormGenerator` with the same factories. The shared `App\Field\FieldTrait` keeps form-type options identical between the two contexts.
