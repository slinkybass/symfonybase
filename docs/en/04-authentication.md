# Authentication

Authentication is built on stock Symfony Security plus SymfonyCasts **reset-password** and **verify-email** bundles, wired together by `App\Controller\AuthController` (`src/Controller/AuthController.php`).

## Security configuration

`config/packages/security.yaml`:

- Password hashers: `auto` for `PasswordAuthenticatedUserInterface` (cost reduced in `when@test`).
- Provider: `app_user_provider` based on `App\Entity\User.email`.
- Single firewall `main` with `lazy: true`, `form_login` (`login_path` / `check_path` both `login`), `logout` on `/logout`, `remember_me`, and `switch_user: { role: ROLE_ADMIN }` for impersonation.
- `access_control`:
    - `^/admin` requires `ROLE_ADMIN` and `%env(REQUIRED_SCHEME)%`.
    - `^/` requires `%env(REQUIRED_SCHEME)%`.

`User` implements `UserInterface` + `PasswordAuthenticatedUserInterface`. Roles returned by `User::getRoles()` come from the linked `Role` (always includes `ROLE_ADMIN` when `Role.isAdmin = true`). Sessions never store the real password hash: `User::__serialize()` substitutes it with a CRC32C of the hash (Symfony 7.3+ pattern).

## Routes (public)

| Route name                    | Path                           | Method                                     |
| ----------------------------- | ------------------------------ | ------------------------------------------ |
| `login`                       | `/login`                       | `AuthController::login`                    |
| `register`                    | `/register`                    | `AuthController::register`                 |
| `verify`                      | `/verify`                      | `AuthController::verify`                   |
| `reset_password_request`      | `/reset-password-request`      | `AuthController::resetPasswordRequest`     |
| `reset_password_request_sent` | `/reset-password-request/sent` | `AuthController::resetPasswordRequestSent` |
| `reset_password`              | `/reset-password/{token}`      | `AuthController::resetPassword`            |

Route visibility (gated by `AppConfig` flags) is enforced by the [AccessSubscriber](06-http-subscribers.md#accesssubscriber).

## Login

`/login` renders the EasyAdmin login page (`@EasyAdmin/page/login.html.twig`) with these tweaks:

- `username_label` is the translated `entities.user.fields.email`.
- `target_path` is the public home (`home`).
- `forgot_password_enabled` follows `appConfig.enableResetPassword`.

After interactive login, [`UserLoginSubscriber`](06-http-subscribers.md#userloginsubscriber) rejects inactive or unverified users with `DisabledException`.

## Registration

`AuthController::register` (`src/Controller/AuthController.php`):

1. Builds `RegistrationForm` (`src/Form/RegistrationForm.php`) on a fresh `User`. The form pulls fields through `App\Form\FormGenerator` and includes a repeated unmapped `plainPassword` plus an unmapped `acceptTerms` switch.
2. On submit, looks up the default role using `AppConfig::roleDefaultRegisterId`. If missing or no longer present, a flash `app.messages.registerMissingDefaultRole` is added and the user is redirected to `register`.
3. Persists the new user with the hashed password, `verified = false`, and the resolved role.
4. Calls `sendVerifyEmail()` which signs a verification URL through `VerifyEmailHelperInterface`, renders `templates/mails/template.html.twig`, and dispatches via `App\Service\MailService`.

The registration link in the login page is conditional on `appConfig.enableRegister`.

## Email verification

`/verify?id=<userId>&...signed-url-payload...`:

- Loads the user by `?id`.
- Validates the signature with `VerifyEmailHelperInterface`.
- Sets `verified = true`, persists, flashes `app.messages.verifyDone` and redirects to `login`.

`InteractiveLogin` rejects unverified accounts (see [UserLoginSubscriber](06-http-subscribers.md#userloginsubscriber)).

## Reset password

`SymfonyCastsResetPasswordBundle` is wired through `App\Repository\ResetPasswordRequestRepository` (configured in `config/packages/reset_password.yaml`) and `App\Entity\ResetPasswordRequest` (uses `ResetPasswordRequestTrait`).

The flow in `AuthController`:

1. `resetPasswordRequest` — collects email through `ResetPasswordRequestForm`, looks the user up via `UserRepository::filterOne([new EmailFilter(...)])`. If the user exists and is inactive/unverified, a translated flash is added; otherwise `sendResetPasswordRequestEmail()` is invoked.
2. `sendResetPasswordRequestEmail` — generates a reset token (when the user exists), renders `mails/template.html.twig`, sends through `MailService`, stores the token object in the session, and always redirects to `reset_password_request_sent` to prevent email enumeration.
3. `resetPasswordRequestSent` — relies on `getTokenObjectFromSession()` and shows `app.messages.resetPasswordRequestSent`.
4. `resetPassword/{token}` — validates the token, builds `ChangePasswordForm`, hashes the new password, removes the request, and redirects to `login` with a success flash.

## Related services

- `App\Repository\UserRepository` implements `PasswordUpgraderInterface` so Symfony can rehash passwords transparently.
- `App\Service\MailService` is responsible for the actual delivery — see [email](14-email.md).
- `App\EventSubscriber\InactiveUserSubscriber` keeps deactivated users out of the application even between login and logout — see [http-subscribers](06-http-subscribers.md).
