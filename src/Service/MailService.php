<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailService
{
    private Request $request;
    private ParameterBagInterface $params;
    private MailerInterface $mailer;

    public function __construct(RequestStack $requestStack, ParameterBagInterface $params, MailerInterface $mailer)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->params = $params;
        $this->mailer = $mailer;
    }

    /**
     * Send an email
     *
     * @param string $subject     the subject of the email
     * @param string $html        the html content of the email
     * @param ?array $emails      the emails to send
     * @param ?array $cc          the cc emails to send
     * @param ?array $cco         the cco emails to send
     * @param ?array $attachments attachments to send
     *
     * @return bool returns true if the email was sent
     */
    public function sendEmail(string $subject, string $html, ?array $emails = [], ?array $cc = [], ?array $cco = [], ?array $attachments = [])
    {
        $config = $this->request->getSession()->get('config');
        $addressFrom = new Address($config->senderEmail, $config->appName);

        $email = (new Email())
            ->from($addressFrom)
            ->subject($subject)
            ->html($html);

        if ($this->params->get('kernel.environment') == "prod") {
            foreach ($emails as $emailAccount) {
                $email = $email->addTo($emailAccount);
            }
            foreach ($cc as $emailAccount) {
                $email = $email->addCc($emailAccount);
            }
            foreach ($cco as $emailAccount) {
                $email = $email->addBcc($emailAccount);
            }
        } else {
            $email = $email->to($config->senderEmail);
        }
        foreach ($attachments as $attachment) {
            $email = $email->attachFromPath($attachment);
        }

        $emailSended = null;
        try {
            $this->mailer->send($email);
            $emailSended = true;
        } catch (TransportExceptionInterface $e) {
            $emailSended = false;
        }

        return $emailSended;
    }
}
