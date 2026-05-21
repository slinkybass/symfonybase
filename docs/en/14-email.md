# Email

The base sends only transactional email out of the box (verification and reset password) using **Symfony Mailer**.

## Transport

`config/packages/mailer.yaml`:

```yaml
framework:
    mailer:
        dsn: "%env(MAILER_DSN)%"
```

`MAILER_DSN` defaults to `null://null`, so no email is sent until you configure a real transport in `.env.local` (SMTP, SendGrid, Mailgun, ...).

## `App\Service\MailService`

`src/Service/MailService.php`. Single entry point for outgoing email.

```php
public function send(
    string $subject,
    string $html,
    array $to = [],
    array $cc = [],
    array $bcc = [],
    array $attachments = [],
): bool;
```

Behavior:

- Builds the `From` address from `AppConfig`: `new Address($config->senderEmail, $config->appName)`.
- In **non-prod environments** every recipient is replaced by `appConfig.senderEmail` so test emails never reach real users.
- In prod, an empty `$to` list is treated as an error (logged and `false`).
- Attachments are added with `attachFromPath()`.
- Transport errors are logged via `LoggerInterface` and swallowed; the method returns `true` only when the transport accepted the message.

## Templates

`templates/mails/`:

- `base.html.twig` — master HTML email skeleton (reusable layout).
- `template.html.twig` — extends the base and renders the standard transactional email shape: subject heading, paragraphs, call-to-action button(s), post-content paragraphs.

`AuthController` renders `template.html.twig` with these variables:

```php
$this->renderView('mails/template.html.twig', [
    'subject'     => ..., // string used as the email H1
    'content'     => [...], // array of paragraphs above the button
    'buttons'     => [label => url, ...],
    'postContent' => [...], // array of paragraphs below the button
]);
```

## Where it is used

Currently only by `App\Controller\AuthController`:

| Email                  | Subject (i18n key)                   | Triggered when                                   |
| ---------------------- | ------------------------------------ | ------------------------------------------------ |
| Email verification     | `email.verify.subject`               | After successful registration                    |
| Reset password request | `email.resetPasswordRequest.subject` | A reset is requested for an active+verified user |

Translations for subject, content paragraphs, button labels, and post-content live under `email.*` in `translations/messages.es.yaml`.

## Adding a new email

1. Translate the subject / body fragments under `email.<name>.*` in `translations/messages.<locale>.yaml`.
2. Build the `subject`, `content`, `buttons`, and `postContent` arrays in the calling service or controller.
3. Render `mails/template.html.twig` (or `mails/base.html.twig` for a bespoke layout).
4. Send through `MailService::send()`.
