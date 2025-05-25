<?php

namespace App\EventSubscriber;

use App\Entity\Config;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ConfigSubscriber implements EventSubscriberInterface
{
    private $em;
    private $config;

    public function __construct(EntityManagerInterface $em, AssetMapperInterface $assetMapper)
    {
        $this->em = $em;

        $this->config = new \stdClass();
        $this->config->appName = 'Symfony Base';
        $this->config->appColor = '#7952B3';
        $this->config->appLogo = $assetMapper->getPublicPath('images/logo.png');
        $this->config->appFavicon = $assetMapper->getPublicPath('images/favicon.png');
        $this->config->appDescription = 'Created with Symfony';
        $this->config->appKeywords = 'symfony, application';
        $this->config->appTimezone = 'Europe/Madrid';
        $this->config->enablePublic = false;
        $this->config->enableCookies = false;
		$this->config->senderEmail = 'israel@garaballu.com';
		$this->config->privacyText = null;
		$this->config->cookiesText = null;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $dcConfig = $this->em->getRepository(Config::class)->get();
        if ($dcConfig) {
            $this->config->appName = $dcConfig->getAppName() ?? $this->config->appName;
            $this->config->appColor = $dcConfig->getAppColor() ?? $this->config->appColor;
            $this->config->appLogo = $dcConfig->getAppLogo() ?? $this->config->appLogo;
            $this->config->appFavicon = $dcConfig->getAppFavicon() ?? $this->config->appFavicon;
            $this->config->appDescription = $dcConfig->getAppDescription() ?? $this->config->appDescription;
            $this->config->appKeywords = $dcConfig->getAppKeywords() ?? $this->config->appKeywords;
            $this->config->appTimezone = $dcConfig->getAppTimezone() ?? $this->config->appTimezone;
            $this->config->enablePublic = $dcConfig->isEnablePublic() ?? $this->config->enablePublic;
            $this->config->enableCookies = $dcConfig->isEnableCookies() ?? $this->config->enableCookies;
			$this->config->senderEmail = $dcConfig->getSenderEmail() ?? $this->config->senderEmail;
            $this->config->privacyText = $dcConfig->getPrivacyText() ?? $this->config->privacyText;
            $this->config->cookiesText = $dcConfig->getCookiesText() ?? $this->config->cookiesText;
        }
        $request->getSession()->set('config', $this->config);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
