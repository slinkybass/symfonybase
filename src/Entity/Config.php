<?php

namespace App\Entity;

use App\Repository\ConfigRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigRepository::class)]
#[ORM\Table(name: "config")]
class Config
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $appName = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $appColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $appLogo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $appFavicon = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $appDescription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $appKeywords = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $appTimezone = null;

    public function __toString(): string
    {
        return $this->appName ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppName(): ?string
    {
        return $this->appName;
    }

    public function setAppName(?string $appName): static
    {
        $this->appName = $appName;

        return $this;
    }

    public function getAppColor(): ?string
    {
        return $this->appColor;
    }

    public function setAppColor(?string $appColor): static
    {
        $this->appColor = $appColor;

        return $this;
    }

    public function getAppLogo(): ?string
    {
        return $this->appLogo;
    }

    public function setAppLogo(?string $appLogo): static
    {
        $this->appLogo = $appLogo;

        return $this;
    }

    public function getAppFavicon(): ?string
    {
        return $this->appFavicon;
    }

    public function setAppFavicon(?string $appFavicon): static
    {
        $this->appFavicon = $appFavicon;

        return $this;
    }

    public function getAppDescription(): ?string
    {
        return $this->appDescription;
    }

    public function setAppDescription(?string $appDescription): static
    {
        $this->appDescription = $appDescription;

        return $this;
    }

    public function getAppKeywords(): ?string
    {
        return $this->appKeywords;
    }

    public function setAppKeywords(?string $appKeywords): static
    {
        $this->appKeywords = $appKeywords;

        return $this;
    }

    public function getAppTimezone(): ?string
    {
        return $this->appTimezone;
    }

    public function setAppTimezone(?string $appTimezone): static
    {
        $this->appTimezone = $appTimezone;

        return $this;
    }
}
