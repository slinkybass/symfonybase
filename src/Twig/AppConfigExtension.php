<?php

namespace App\Twig;

use App\Service\ConfigService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Exposes the resolved application configuration DTO to every Twig template as `appConfig`.
 */
class AppConfigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private readonly ConfigService $configService)
    {
    }

    /**
     * @return array{appConfig: \App\Model\AppConfig}
     */
    public function getGlobals(): array
    {
        return [
            'appConfig' => $this->configService->get(),
        ];
    }
}
