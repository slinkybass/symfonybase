<?php

namespace App\Twig;

use App\Service\ConfigService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Twig extension to access app config in Twig templates.
 */
class AppConfigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private readonly ConfigService $configService)
    {
    }

    public function getGlobals(): array
    {
        return [
            'appConfig' => $this->configService->get(),
        ];
    }
}
