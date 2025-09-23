<?php

namespace App\Entity;

use App\Entity\Enum\UserGender;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: "demoEntity")]
class DemoEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $text = null;

    #[ORM\Column(length: 255)]
    private ?string $text2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $textHelp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $confirmPassword = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $confirmEmail = null;

    #[ORM\Column(nullable: true)]
    private ?int $integerNumber = null;

    #[ORM\Column(nullable: true)]
    private ?int $integerPositive = null;

    #[ORM\Column(nullable: true)]
    private ?float $floatNumber = null;

    #[ORM\Column(nullable: true)]
    private ?float $floatPositive = null;

    #[ORM\Column(nullable: true)]
    private ?float $floatStep05 = null;

    #[ORM\Column(nullable: true)]
    private ?float $percent = null;

    #[ORM\Column(nullable: true)]
    private ?float $money = null;

    #[ORM\Column(nullable: true)]
    private ?float $moneyCustomCurrency = null;

    #[ORM\Column(nullable: true)]
    private ?int $slider = null;

    #[ORM\Column(nullable: true)]
    private ?float $sliderFloat = null;

    #[ORM\Column(nullable: true)]
    private ?bool $checkbox = null;

    #[ORM\Column(nullable: true)]
    private ?bool $switch = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorPalette = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorPaletteOnly = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorInline = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorInlinePalette = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorInlinePaletteOnly = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorAlpha = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mask = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mask2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mask3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugTarget = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textarea = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textarea2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textarea3 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textarea4 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditor = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditor2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditor3 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $codeeditor = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $codeeditor2 = null;

    #[ORM\Column(length: 255, nullable: true, enumType: UserGender::class)]
    private ?UserGender $choice = null;

    #[ORM\Column(length: 255, nullable: true, enumType: UserGender::class)]
    private ?UserGender $choice2 = null;

    #[ORM\Column(length: 255, nullable: true, enumType: UserGender::class)]
    private ?UserGender $choice3 = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $choice4 = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $choice5 = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datetime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $time = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDefaultValue = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datetimeDefaultValue = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timeDefaultValue = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateMinMax = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datetimeMinMax = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timeMinMax = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnable = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDisable = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateInline = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datetimeInline = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timeInline = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $dateMultiple = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $datetimeMultiple = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $dateMultipleCustom = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $datetimeMultipleCustom = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $dateRange = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $datetimeRange = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $dateRangeCustom = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $array = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $collection = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $collectionFormType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $media = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mediaImage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mediaImageUsers = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mediaWithoutFileManager = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mediaWithCrop = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $file = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filePDF = null;

    #[ORM\Column]
    private ?bool $hierarchySwitch = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hierarchySwitchChild = null;

    #[ORM\Column]
    private ?bool $hierarchySwitchChecked = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hierarchySwitchCheckedChild = null;

    #[ORM\Column]
    private ?bool $hierarchySwitchShow = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hierarchySwitchShowChild = null;

    #[ORM\Column]
    private ?bool $hierarchySwitchDefaultValue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hierarchySwitchDefaultValueChild = null;

    #[ORM\Column]
    private ?bool $hierarchySwitchInvertValue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hierarchySwitchInvertValueChild = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hierarchyText = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hierarchyTextChild = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hierarchyTextCustomValue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hierarchyTextCustomValueChild = null;

    #[ORM\Column]
    private ?bool $hierarchyKeepValue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hierarchyKeepValueChild = null;

    #[ORM\Column]
    private ?bool $hierarchySaveValue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hierarchySaveValueChild = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signature = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signatureWithInput = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signatureWithoutUndo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signatureWithoutClear = null;

    public function __toString(): string
    {
        return strval($this->getId()) ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getText2(): ?string
    {
        return $this->text2;
    }

    public function setText2(string $text2): static
    {
        $this->text2 = $text2;

        return $this;
    }

    public function getTextHelp(): ?string
    {
        return $this->textHelp;
    }

    public function setTextHelp(?string $textHelp): static
    {
        $this->textHelp = $textHelp;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(?string $confirmPassword): static
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    public function getConfirmEmail(): ?string
    {
        return $this->confirmEmail;
    }

    public function setConfirmEmail(?string $confirmEmail): static
    {
        $this->confirmEmail = $confirmEmail;

        return $this;
    }

    public function getIntegerNumber(): ?int
    {
        return $this->integerNumber;
    }

    public function setIntegerNumber(?int $integerNumber): static
    {
        $this->integerNumber = $integerNumber;

        return $this;
    }

    public function getIntegerPositive(): ?int
    {
        return $this->integerPositive;
    }

    public function setIntegerPositive(?int $integerPositive): static
    {
        $this->integerPositive = $integerPositive;

        return $this;
    }

    public function getFloatNumber(): ?float
    {
        return $this->floatNumber;
    }

    public function setFloatNumber(?float $floatNumber): static
    {
        $this->floatNumber = $floatNumber;

        return $this;
    }

    public function getFloatPositive(): ?float
    {
        return $this->floatPositive;
    }

    public function setFloatPositive(?float $floatPositive): static
    {
        $this->floatPositive = $floatPositive;

        return $this;
    }

    public function getFloatStep05(): ?float
    {
        return $this->floatStep05;
    }

    public function setFloatStep05(?float $floatStep05): static
    {
        $this->floatStep05 = $floatStep05;

        return $this;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(?float $percent): static
    {
        $this->percent = $percent;

        return $this;
    }

    public function getMoney(): ?float
    {
        return $this->money;
    }

    public function setMoney(?float $money): static
    {
        $this->money = $money;

        return $this;
    }

    public function getMoneyCustomCurrency(): ?float
    {
        return $this->moneyCustomCurrency;
    }

    public function setMoneyCustomCurrency(?float $moneyCustomCurrency): static
    {
        $this->moneyCustomCurrency = $moneyCustomCurrency;

        return $this;
    }

    public function getSlider(): ?int
    {
        return $this->slider;
    }

    public function setSlider(?int $slider): static
    {
        $this->slider = $slider;

        return $this;
    }

    public function getSliderFloat(): ?float
    {
        return $this->sliderFloat;
    }

    public function setSliderFloat(?float $sliderFloat): static
    {
        $this->sliderFloat = $sliderFloat;

        return $this;
    }

    public function isCheckbox(): ?bool
    {
        return $this->checkbox;
    }

    public function setCheckbox(?bool $checkbox): static
    {
        $this->checkbox = $checkbox;

        return $this;
    }

    public function isSwitch(): ?bool
    {
        return $this->switch;
    }

    public function setSwitch(?bool $switch): static
    {
        $this->switch = $switch;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getColorPalette(): ?string
    {
        return $this->colorPalette;
    }

    public function setColorPalette(?string $colorPalette): static
    {
        $this->colorPalette = $colorPalette;

        return $this;
    }

    public function getColorPaletteOnly(): ?string
    {
        return $this->colorPaletteOnly;
    }

    public function setColorPaletteOnly(?string $colorPaletteOnly): static
    {
        $this->colorPaletteOnly = $colorPaletteOnly;

        return $this;
    }

    public function getColorInline(): ?string
    {
        return $this->colorInline;
    }

    public function setColorInline(?string $colorInline): static
    {
        $this->colorInline = $colorInline;

        return $this;
    }

    public function getColorInlinePalette(): ?string
    {
        return $this->colorInlinePalette;
    }

    public function setColorInlinePalette(?string $colorInlinePalette): static
    {
        $this->colorInlinePalette = $colorInlinePalette;

        return $this;
    }

    public function getColorInlinePaletteOnly(): ?string
    {
        return $this->colorInlinePaletteOnly;
    }

    public function setColorInlinePaletteOnly(?string $colorInlinePaletteOnly): static
    {
        $this->colorInlinePaletteOnly = $colorInlinePaletteOnly;

        return $this;
    }

    public function getColorAlpha(): ?string
    {
        return $this->colorAlpha;
    }

    public function setColorAlpha(?string $colorAlpha): static
    {
        $this->colorAlpha = $colorAlpha;

        return $this;
    }

    public function getMask(): ?string
    {
        return $this->mask;
    }

    public function setMask(?string $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    public function getMask2(): ?string
    {
        return $this->mask2;
    }

    public function setMask2(?string $mask2): static
    {
        $this->mask2 = $mask2;

        return $this;
    }

    public function getMask3(): ?string
    {
        return $this->mask3;
    }

    public function setMask3(?string $mask3): static
    {
        $this->mask3 = $mask3;

        return $this;
    }

    public function getSlugTarget(): ?string
    {
        return $this->slugTarget;
    }

    public function setSlugTarget(?string $slugTarget): static
    {
        $this->slugTarget = $slugTarget;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug2(): ?string
    {
        return $this->slug2;
    }

    public function setSlug2(?string $slug2): static
    {
        $this->slug2 = $slug2;

        return $this;
    }

    public function getTextarea(): ?string
    {
        return $this->textarea;
    }

    public function setTextarea(?string $textarea): static
    {
        $this->textarea = $textarea;

        return $this;
    }

    public function getTextarea2(): ?string
    {
        return $this->textarea2;
    }

    public function setTextarea2(?string $textarea2): static
    {
        $this->textarea2 = $textarea2;

        return $this;
    }

    public function getTextarea3(): ?string
    {
        return $this->textarea3;
    }

    public function setTextarea3(?string $textarea3): static
    {
        $this->textarea3 = $textarea3;

        return $this;
    }

    public function getTextarea4(): ?string
    {
        return $this->textarea4;
    }

    public function setTextarea4(?string $textarea4): static
    {
        $this->textarea4 = $textarea4;

        return $this;
    }

    public function getTexteditor(): ?string
    {
        return $this->texteditor;
    }

    public function setTexteditor(?string $texteditor): static
    {
        $this->texteditor = $texteditor;

        return $this;
    }

    public function getTexteditor2(): ?string
    {
        return $this->texteditor2;
    }

    public function setTexteditor2(?string $texteditor2): static
    {
        $this->texteditor2 = $texteditor2;

        return $this;
    }

    public function getTexteditor3(): ?string
    {
        return $this->texteditor3;
    }

    public function setTexteditor3(?string $texteditor3): static
    {
        $this->texteditor3 = $texteditor3;

        return $this;
    }

    public function getCodeeditor(): ?string
    {
        return $this->codeeditor;
    }

    public function setCodeeditor(?string $codeeditor): static
    {
        $this->codeeditor = $codeeditor;

        return $this;
    }

    public function getCodeeditor2(): ?string
    {
        return $this->codeeditor2;
    }

    public function setCodeeditor2(?string $codeeditor2): static
    {
        $this->codeeditor2 = $codeeditor2;

        return $this;
    }

    public function getChoice(): ?UserGender
    {
        return $this->choice;
    }

    public function setChoice(?UserGender $choice): static
    {
        $this->choice = $choice;

        return $this;
    }

    public function getChoice2(): ?UserGender
    {
        return $this->choice2;
    }

    public function setChoice2(?UserGender $choice2): static
    {
        $this->choice2 = $choice2;

        return $this;
    }

    public function getChoice3(): ?UserGender
    {
        return $this->choice3;
    }

    public function setChoice3(?UserGender $choice3): static
    {
        $this->choice3 = $choice3;

        return $this;
    }

    public function getChoice4(): ?array
    {
        return $this->choice4;
    }

    public function setChoice4(?array $choice4): static
    {
        $this->choice4 = $choice4;

        return $this;
    }

    public function getChoice5(): ?array
    {
        return $this->choice5;
    }

    public function setChoice5(?array $choice5): static
    {
        $this->choice5 = $choice5;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(?\DateTimeInterface $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?\DateTimeInterface $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getDateDefaultValue(): ?\DateTimeInterface
    {
        return $this->dateDefaultValue;
    }

    public function setDateDefaultValue(?\DateTimeInterface $dateDefaultValue): static
    {
        $this->dateDefaultValue = $dateDefaultValue;

        return $this;
    }

    public function getDatetimeDefaultValue(): ?\DateTimeInterface
    {
        return $this->datetimeDefaultValue;
    }

    public function setDatetimeDefaultValue(?\DateTimeInterface $datetimeDefaultValue): static
    {
        $this->datetimeDefaultValue = $datetimeDefaultValue;

        return $this;
    }

    public function getTimeDefaultValue(): ?\DateTimeInterface
    {
        return $this->timeDefaultValue;
    }

    public function setTimeDefaultValue(?\DateTimeInterface $timeDefaultValue): static
    {
        $this->timeDefaultValue = $timeDefaultValue;

        return $this;
    }

    public function getDateMinMax(): ?\DateTimeInterface
    {
        return $this->dateMinMax;
    }

    public function setDateMinMax(?\DateTimeInterface $dateMinMax): static
    {
        $this->dateMinMax = $dateMinMax;

        return $this;
    }

    public function getDatetimeMinMax(): ?\DateTimeInterface
    {
        return $this->datetimeMinMax;
    }

    public function setDatetimeMinMax(?\DateTimeInterface $datetimeMinMax): static
    {
        $this->datetimeMinMax = $datetimeMinMax;

        return $this;
    }

    public function getTimeMinMax(): ?\DateTimeInterface
    {
        return $this->timeMinMax;
    }

    public function setTimeMinMax(?\DateTimeInterface $timeMinMax): static
    {
        $this->timeMinMax = $timeMinMax;

        return $this;
    }

    public function getDateEnable(): ?\DateTimeInterface
    {
        return $this->dateEnable;
    }

    public function setDateEnable(?\DateTimeInterface $dateEnable): static
    {
        $this->dateEnable = $dateEnable;

        return $this;
    }

    public function getDateDisable(): ?\DateTimeInterface
    {
        return $this->dateDisable;
    }

    public function setDateDisable(?\DateTimeInterface $dateDisable): static
    {
        $this->dateDisable = $dateDisable;

        return $this;
    }

    public function getDateInline(): ?\DateTimeInterface
    {
        return $this->dateInline;
    }

    public function setDateInline(?\DateTimeInterface $dateInline): static
    {
        $this->dateInline = $dateInline;

        return $this;
    }

    public function getDatetimeInline(): ?\DateTimeInterface
    {
        return $this->datetimeInline;
    }

    public function setDatetimeInline(?\DateTimeInterface $datetimeInline): static
    {
        $this->datetimeInline = $datetimeInline;

        return $this;
    }

    public function getTimeInline(): ?\DateTimeInterface
    {
        return $this->timeInline;
    }

    public function setTimeInline(?\DateTimeInterface $timeInline): static
    {
        $this->timeInline = $timeInline;

        return $this;
    }

    public function getDateMultiple(): ?array
    {
        return $this->dateMultiple;
    }

    public function setDateMultiple(?array $dateMultiple): static
    {
        $this->dateMultiple = $dateMultiple;

        return $this;
    }

    public function getDatetimeMultiple(): ?array
    {
        return $this->datetimeMultiple;
    }

    public function setDatetimeMultiple(?array $datetimeMultiple): static
    {
        $this->datetimeMultiple = $datetimeMultiple;

        return $this;
    }

    public function getDateMultipleCustom(): ?array
    {
        return $this->dateMultipleCustom;
    }

    public function setDateMultipleCustom(?array $dateMultipleCustom): static
    {
        $this->dateMultipleCustom = $dateMultipleCustom;

        return $this;
    }

    public function getDatetimeMultipleCustom(): ?array
    {
        return $this->datetimeMultipleCustom;
    }

    public function setDatetimeMultipleCustom(?array $datetimeMultipleCustom): static
    {
        $this->datetimeMultipleCustom = $datetimeMultipleCustom;

        return $this;
    }

    public function getDateRange(): ?array
    {
        return $this->dateRange;
    }

    public function setDateRange(?array $dateRange): static
    {
        $this->dateRange = $dateRange;

        return $this;
    }

    public function getDatetimeRange(): ?array
    {
        return $this->datetimeRange;
    }

    public function setDatetimeRange(?array $datetimeRange): static
    {
        $this->datetimeRange = $datetimeRange;

        return $this;
    }

    public function getDateRangeCustom(): ?array
    {
        return $this->dateRangeCustom;
    }

    public function setDateRangeCustom(?array $dateRangeCustom): static
    {
        $this->dateRangeCustom = $dateRangeCustom;

        return $this;
    }

    public function getArray(): ?array
    {
        return $this->array;
    }

    public function setArray(?array $array): static
    {
        $this->array = $array;

        return $this;
    }

    public function getCollection(): ?array
    {
        return $this->collection;
    }

    public function setCollection(?array $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function getCollectionFormType(): ?array
    {
        return $this->collectionFormType;
    }

    public function setCollectionFormType(?array $collectionFormType): static
    {
        $this->collectionFormType = $collectionFormType;

        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(?string $media): static
    {
        $this->media = $media;

        return $this;
    }

    public function getMediaImage(): ?string
    {
        return $this->mediaImage;
    }

    public function setMediaImage(?string $mediaImage): static
    {
        $this->mediaImage = $mediaImage;

        return $this;
    }

    public function getMediaImageUsers(): ?string
    {
        return $this->mediaImageUsers;
    }

    public function setMediaImageUsers(?string $mediaImageUsers): static
    {
        $this->mediaImageUsers = $mediaImageUsers;

        return $this;
    }

    public function getMediaWithoutFileManager(): ?string
    {
        return $this->mediaWithoutFileManager;
    }

    public function setMediaWithoutFileManager(?string $mediaWithoutFileManager): static
    {
        $this->mediaWithoutFileManager = $mediaWithoutFileManager;

        return $this;
    }

    public function getMediaWithCrop(): ?string
    {
        return $this->mediaWithCrop;
    }

    public function setMediaWithCrop(?string $mediaWithCrop): static
    {
        $this->mediaWithCrop = $mediaWithCrop;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getFilePDF(): ?string
    {
        return $this->filePDF;
    }

    public function setFilePDF(?string $filePDF): static
    {
        $this->filePDF = $filePDF;

        return $this;
    }

    public function isHierarchySwitch(): ?bool
    {
        return $this->hierarchySwitch;
    }

    public function setHierarchySwitch(bool $hierarchySwitch): static
    {
        $this->hierarchySwitch = $hierarchySwitch;

        return $this;
    }

    public function getHierarchySwitchChild(): ?string
    {
        return $this->hierarchySwitchChild;
    }

    public function setHierarchySwitchChild(?string $hierarchySwitchChild): static
    {
        $this->hierarchySwitchChild = $hierarchySwitchChild;

        return $this;
    }

    public function isHierarchySwitchChecked(): ?bool
    {
        return $this->hierarchySwitchChecked;
    }

    public function setHierarchySwitchChecked(bool $hierarchySwitchChecked): static
    {
        $this->hierarchySwitchChecked = $hierarchySwitchChecked;

        return $this;
    }

    public function getHierarchySwitchCheckedChild(): ?\DateTimeInterface
    {
        return $this->hierarchySwitchCheckedChild;
    }

    public function setHierarchySwitchCheckedChild(?\DateTimeInterface $hierarchySwitchCheckedChild): static
    {
        $this->hierarchySwitchCheckedChild = $hierarchySwitchCheckedChild;

        return $this;
    }

    public function isHierarchySwitchShow(): ?bool
    {
        return $this->hierarchySwitchShow;
    }

    public function setHierarchySwitchShow(bool $hierarchySwitchShow): static
    {
        $this->hierarchySwitchShow = $hierarchySwitchShow;

        return $this;
    }

    public function getHierarchySwitchShowChild(): ?string
    {
        return $this->hierarchySwitchShowChild;
    }

    public function setHierarchySwitchShowChild(?string $hierarchySwitchShowChild): static
    {
        $this->hierarchySwitchShowChild = $hierarchySwitchShowChild;

        return $this;
    }

    public function isHierarchySwitchDefaultValue(): ?bool
    {
        return $this->hierarchySwitchDefaultValue;
    }

    public function setHierarchySwitchDefaultValue(bool $hierarchySwitchDefaultValue): static
    {
        $this->hierarchySwitchDefaultValue = $hierarchySwitchDefaultValue;

        return $this;
    }

    public function getHierarchySwitchDefaultValueChild(): ?string
    {
        return $this->hierarchySwitchDefaultValueChild;
    }

    public function setHierarchySwitchDefaultValueChild(?string $hierarchySwitchDefaultValueChild): static
    {
        $this->hierarchySwitchDefaultValueChild = $hierarchySwitchDefaultValueChild;

        return $this;
    }

    public function isHierarchySwitchInvertValue(): ?bool
    {
        return $this->hierarchySwitchInvertValue;
    }

    public function setHierarchySwitchInvertValue(bool $hierarchySwitchInvertValue): static
    {
        $this->hierarchySwitchInvertValue = $hierarchySwitchInvertValue;

        return $this;
    }

    public function getHierarchySwitchInvertValueChild(): ?string
    {
        return $this->hierarchySwitchInvertValueChild;
    }

    public function setHierarchySwitchInvertValueChild(?string $hierarchySwitchInvertValueChild): static
    {
        $this->hierarchySwitchInvertValueChild = $hierarchySwitchInvertValueChild;

        return $this;
    }

    public function getHierarchyText(): ?string
    {
        return $this->hierarchyText;
    }

    public function setHierarchyText(?string $hierarchyText): static
    {
        $this->hierarchyText = $hierarchyText;

        return $this;
    }

    public function getHierarchyTextChild(): ?string
    {
        return $this->hierarchyTextChild;
    }

    public function setHierarchyTextChild(?string $hierarchyTextChild): static
    {
        $this->hierarchyTextChild = $hierarchyTextChild;

        return $this;
    }

    public function getHierarchyTextCustomValue(): ?string
    {
        return $this->hierarchyTextCustomValue;
    }

    public function setHierarchyTextCustomValue(?string $hierarchyTextCustomValue): static
    {
        $this->hierarchyTextCustomValue = $hierarchyTextCustomValue;

        return $this;
    }

    public function getHierarchyTextCustomValueChild(): ?string
    {
        return $this->hierarchyTextCustomValueChild;
    }

    public function setHierarchyTextCustomValueChild(?string $hierarchyTextCustomValueChild): static
    {
        $this->hierarchyTextCustomValueChild = $hierarchyTextCustomValueChild;

        return $this;
    }

    public function getHierarchyKeepValue(): ?bool
    {
        return $this->hierarchyKeepValue;
    }

    public function setHierarchyKeepValue(?bool $hierarchyKeepValue): static
    {
        $this->hierarchyKeepValue = $hierarchyKeepValue;

        return $this;
    }

    public function getHierarchyKeepValueChild(): ?string
    {
        return $this->hierarchyKeepValueChild;
    }

    public function setHierarchyKeepValueChild(?string $hierarchyKeepValueChild): static
    {
        $this->hierarchyKeepValueChild = $hierarchyKeepValueChild;

        return $this;
    }

    public function isHierarchySaveValue(): ?bool
    {
        return $this->hierarchySaveValue;
    }

    public function setHierarchySaveValue(bool $hierarchySaveValue): static
    {
        $this->hierarchySaveValue = $hierarchySaveValue;

        return $this;
    }

    public function getHierarchySaveValueChild(): ?string
    {
        return $this->hierarchySaveValueChild;
    }

    public function setHierarchySaveValueChild(?string $hierarchySaveValueChild): static
    {
        $this->hierarchySaveValueChild = $hierarchySaveValueChild;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): static
    {
        $this->signature = $signature;

        return $this;
    }

    public function getSignatureWithInput(): ?string
    {
        return $this->signatureWithInput;
    }

    public function setSignatureWithInput(?string $signatureWithInput): static
    {
        $this->signatureWithInput = $signatureWithInput;

        return $this;
    }

    public function getSignatureWithoutUndo(): ?string
    {
        return $this->signatureWithoutUndo;
    }

    public function setSignatureWithoutUndo(?string $signatureWithoutUndo): static
    {
        $this->signatureWithoutUndo = $signatureWithoutUndo;

        return $this;
    }

    public function getSignatureWithoutClear(): ?string
    {
        return $this->signatureWithoutClear;
    }

    public function setSignatureWithoutClear(?string $signatureWithoutClear): static
    {
        $this->signatureWithoutClear = $signatureWithoutClear;

        return $this;
    }
}
