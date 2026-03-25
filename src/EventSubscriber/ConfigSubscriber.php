<?php

namespace App\EventSubscriber;

use App\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Loads application configuration on every main request and stores it in the session.
 *
 * Default values are defined inline and overridden by any Config entity found in the
 * database. The resulting config object is stored under the 'config' session key and
 * consumed by other services (e.g. MailService) and templates throughout the request.
 *
 * Only the master request is processed; internal sub-requests are ignored to avoid
 * redundant database queries and unintended session writes.
 */
class ConfigSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AssetMapperInterface $assetMapper,
    ) {
    }

    /**
     * Builds a config object populated with application defaults.
     *
     * @return \stdClass the default config object
     */
    private function buildDefaultConfig(): \stdClass
    {
        $config = new \stdClass();

        $config->appName = 'Symfony Base';
        $config->appColor = '#22a6b3';
        $config->appLogo = $this->assetMapper->getPublicPath('images/logo.png');
        $config->appFavicon = $this->assetMapper->getPublicPath('images/favicon.png');
        $config->appDescription = 'Created with Symfony';
        $config->appKeywords = 'symfony, application';
        $config->appTimezone = 'Europe/Madrid';
        $config->enablePublic = false;
        $config->enableResetPassword = false;
        $config->enableRegister = false;
        $config->roleDefaultRegister = null;
        $config->enableCookies = false;
        $config->senderEmail = 'israel@garaballu.com';
        $config->privacyText = null;
        $config->cookiesText = null;

        return $config;
    }

    /**
     * Builds the config object and stores it in the session.
     *
     * @param RequestEvent $event the kernel request event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $config = $this->buildDefaultConfig();

        $dbConfig = $this->em->getRepository(Config::class)->get();

        if ($dbConfig) {
            $config->appName = $dbConfig->getAppName() ?? $config->appName;
            $config->appColor = $dbConfig->getAppColor() ?? $config->appColor;
            $config->appLogo = $dbConfig->getAppLogo() ?? $config->appLogo;
            $config->appFavicon = $dbConfig->getAppFavicon() ?? $config->appFavicon;
            $config->appDescription = $dbConfig->getAppDescription() ?? $config->appDescription;
            $config->appKeywords = $dbConfig->getAppKeywords() ?? $config->appKeywords;
            $config->appTimezone = $dbConfig->getAppTimezone() ?? $config->appTimezone;
            $config->enablePublic = $dbConfig->isEnablePublic() ?? $config->enablePublic;
            $config->enableResetPassword = $dbConfig->isEnableResetPassword() ?? $config->enableResetPassword;
            $config->enableRegister = $dbConfig->isEnableRegister() ?? $config->enableRegister;
            $config->roleDefaultRegister = $dbConfig->getRoleDefaultRegister() ?? $config->roleDefaultRegister;
            $config->enableCookies = $dbConfig->isEnableCookies() ?? $config->enableCookies;
            $config->senderEmail = $dbConfig->getSenderEmail() ?? $config->senderEmail;
            $config->privacyText = $dbConfig->getPrivacyText() ?? $config->privacyText;
            $config->cookiesText = $dbConfig->getCookiesText() ?? $config->cookiesText;
        }

        $event->getRequest()->getSession()->set('config', $config);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
