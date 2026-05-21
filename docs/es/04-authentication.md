# Autenticación

La autenticación se construye sobre el componente de seguridad estándar de Symfony junto con los bundles **reset-password** y **verify-email** de SymfonyCasts, integrados por `App\Controller\AuthController` (`src/Controller/AuthController.php`).

## Configuración de seguridad

`config/packages/security.yaml`:

- Hashers de contraseña: `auto` para `PasswordAuthenticatedUserInterface` (coste reducido en `when@test`).
- Proveedor: `app_user_provider` basado en `App\Entity\User.email`.
- Firewall único `main` con `lazy: true`, `form_login` (`login_path` / `check_path` ambos en `login`), `logout` en `/logout`, `remember_me` y `switch_user: { role: ROLE_ADMIN }` para suplantación de identidad.
- `access_control`:
    - `^/admin` requiere `ROLE_ADMIN` y `%env(REQUIRED_SCHEME)%`.
    - `^/` requiere `%env(REQUIRED_SCHEME)%`.

`User` implementa `UserInterface` + `PasswordAuthenticatedUserInterface`. Los roles devueltos por `User::getRoles()` provienen del `Role` vinculado (siempre incluye `ROLE_ADMIN` cuando `Role.isAdmin = true`). Las sesiones nunca almacenan el hash real de la contraseña: `User::__serialize()` lo sustituye por un CRC32C del hash (patrón de Symfony 7.3+).

## Rutas (públicas)

| Nombre de ruta                | Ruta                           | Método                                     |
| ----------------------------- | ------------------------------ | ------------------------------------------ |
| `login`                       | `/login`                       | `AuthController::login`                    |
| `register`                    | `/register`                    | `AuthController::register`                 |
| `verify`                      | `/verify`                      | `AuthController::verify`                   |
| `reset_password_request`      | `/reset-password-request`      | `AuthController::resetPasswordRequest`     |
| `reset_password_request_sent` | `/reset-password-request/sent` | `AuthController::resetPasswordRequestSent` |
| `reset_password`              | `/reset-password/{token}`      | `AuthController::resetPassword`            |

La visibilidad de las rutas (controlada por los flags de `AppConfig`) la gestiona el [AccessSubscriber](06-http-subscribers.md#accesssubscriber).

## Login

`/login` renderiza la página de login de EasyAdmin (`@EasyAdmin/page/login.html.twig`) con estos ajustes:

- `username_label` es el texto traducido de `entities.user.fields.email`.
- `target_path` apunta a la portada pública (`home`).
- `forgot_password_enabled` sigue el valor de `appConfig.enableResetPassword`.

Tras el login interactivo, el [`UserLoginSubscriber`](06-http-subscribers.md#userloginsubscriber) rechaza a los usuarios inactivos o no verificados con una `DisabledException`.

## Registro

`AuthController::register` (`src/Controller/AuthController.php`):

1. Construye `RegistrationForm` (`src/Form/RegistrationForm.php`) sobre un `User` nuevo. El formulario obtiene los campos a través de `App\Form\FormGenerator` e incluye un `plainPassword` repetido no mapeado y un switch `acceptTerms` también no mapeado.
2. Al enviar, busca el rol por defecto usando `AppConfig::roleDefaultRegisterId`. Si no existe o ya no está disponible, se añade un flash `app.messages.registerMissingDefaultRole` y se redirige a `register`.
3. Persiste el nuevo usuario con la contraseña hasheada, `verified = false` y el rol resuelto.
4. Llama a `sendVerifyEmail()`, que firma una URL de verificación mediante `VerifyEmailHelperInterface`, renderiza `templates/mails/template.html.twig` y la envía a través de `App\Service\MailService`.

El enlace de registro en la página de login es condicional según `appConfig.enableRegister`.

## Verificación de email

`/verify?id=<userId>&...signed-url-payload...`:

- Carga el usuario por `?id`.
- Valida la firma con `VerifyEmailHelperInterface`.
- Establece `verified = true`, persiste, añade el flash `app.messages.verifyDone` y redirige a `login`.

El login interactivo rechaza las cuentas no verificadas (ver [UserLoginSubscriber](06-http-subscribers.md#userloginsubscriber)).

## Recuperación de contraseña

`SymfonyCastsResetPasswordBundle` se conecta a través de `App\Repository\ResetPasswordRequestRepository` (configurado en `config/packages/reset_password.yaml`) y `App\Entity\ResetPasswordRequest` (usa `ResetPasswordRequestTrait`).

El flujo en `AuthController`:

1. `resetPasswordRequest` — recoge el email mediante `ResetPasswordRequestForm` y busca el usuario con `UserRepository::filterOne([new EmailFilter(...)])`. Si el usuario existe pero está inactivo o no verificado, se añade un flash traducido; en caso contrario se invoca `sendResetPasswordRequestEmail()`.
2. `sendResetPasswordRequestEmail` — genera un token de recuperación (cuando el usuario existe), renderiza `mails/template.html.twig`, lo envía a través de `MailService`, almacena el objeto token en la sesión y redirige siempre a `reset_password_request_sent` para evitar la enumeración de emails.
3. `resetPasswordRequestSent` — depende de `getTokenObjectFromSession()` y muestra `app.messages.resetPasswordRequestSent`.
4. `resetPassword/{token}` — valida el token, construye `ChangePasswordForm`, hashea la nueva contraseña, elimina la solicitud y redirige a `login` con un flash de éxito.

## Servicios relacionados

- `App\Repository\UserRepository` implementa `PasswordUpgraderInterface` para que Symfony pueda rehaschear contraseñas de forma transparente.
- `App\Service\MailService` se encarga del envío real de correos — ver [email](14-email.md).
- `App\EventSubscriber\InactiveUserSubscriber` mantiene a los usuarios desactivados fuera de la aplicación incluso entre el login y el logout — ver [http-subscribers](06-http-subscribers.md).
