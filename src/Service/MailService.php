<?php

namespace App\Service;

use App\Model\AppConfig;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Handles outgoing email delivery for the application.
 *
 * Reads the sender address and application name from ConfigService (resolved AppConfig),
 * and delegates transport to Symfony Mailer.
 *
 * In non-production environments all recipients are replaced by the configured
 * sender address, so test emails never reach real users.
 */
final readonly class MailService
{
    public function __construct(
        private ParameterBagInterface $params,
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private ConfigService $configService,
    ) {
    }

    /**
     * Sends via Symfony Mailer using `AppConfig` for From name/address. Transport errors are logged and swallowed.
     *
     * @param list<string> $to
     * @param list<string> $cc
     * @param list<string> $bcc
     * @param list<string> $attachments absolute filesystem paths
     *
     * @return bool whether the transport accepted the message (always false when prod has no `to`)
     */
    public function send(
        string $subject,
        string $html,
        array $to = [],
        array $cc = [],
        array $bcc = [],
        array $attachments = [],
    ): bool {
        $config = $this->configService->get();

        $isProd = $this->params->get('kernel.environment') === 'prod';

        if ($isProd && empty($to)) {
            $this->logger->error('MailService: no recipients provided.');

            return false;
        }

        $email = (new Email())
            ->from(new Address($config->senderEmail, $config->appName))
            ->subject($subject)
            ->html($html);

        if ($isProd) {
            foreach ($to as $recipient) {
                $email->addTo($recipient);
            }
            foreach ($cc as $recipient) {
                $email->addCc($recipient);
            }
            foreach ($bcc as $recipient) {
                $email->addBcc($recipient);
            }
        } else {
            $email->to($config->senderEmail);
        }

        foreach ($attachments as $path) {
            $email->attachFromPath($path);
        }

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('MailService: failed to send email.', [
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
