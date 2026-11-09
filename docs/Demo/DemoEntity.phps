<?php

namespace App\Entity;

use App\Entity\Enum\UserGender;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'demoEntity')]
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
    private ?string $textRequired = null;

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
    private ?float $floatStepHalf = null;

    #[ORM\Column(nullable: true)]
    private ?float $percent = null;

    #[ORM\Column(nullable: true)]
    private ?float $money = null;

    #[ORM\Column(nullable: true)]
    private ?float $moneyCustomCurrency = null;

    #[ORM\Column(nullable: true)]
    private ?bool $checkbox = null;

    #[ORM\Column(nullable: true)]
    private ?bool $switch = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $file = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filePDF = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textarea = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textareaBounds = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textareaNoAutogrow = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textareaStatic = null;

    #[ORM\Column(length: 255, nullable: true, enumType: UserGender::class)]
    private ?UserGender $choice = null;

    #[ORM\Column(length: 255, nullable: true, enumType: UserGender::class)]
    private ?UserGender $choiceExpanded = null;

    #[ORM\Column(length: 255, nullable: true, enumType: UserGender::class)]
    private ?UserGender $choiceExpandedRequired = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $choiceMultiple = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $choiceMultipleExpanded = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $selectDefault = null;

    #[ORM\Column(nullable: true, enumType: UserGender::class)]
    private ?UserGender $selectEnum = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $selectRequired = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $selectCreate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $selectCreateOnBlur = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $selectHtml = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $selectMultiple = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $selectMaxOptions = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $selectMaxItems = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $selectOnTab = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $selectDiacritics = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $selectNoClear = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $selectNoDropdown = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $selectCloseAfterSelect = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $selectHideSelected = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $selectCheckbox = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $selectNoRemove = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $selectDuplicates = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Role $selectRole = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $selectUser = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $codeeditor = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $codeeditorPhpGithub = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $codeeditorPlaceholder = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $codeeditorBounds = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $codeeditorIndentSpaces = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $codeeditorWrap = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $codeeditorPrintMargin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $codeeditorMinimalUi = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $codeeditorReadonly = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $array = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $collection = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $collectionNoAdd = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $collectionNoDelete = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $collectionLabels = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $collectionPlugins = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorFlat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorAlpha = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorButtons = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorPalette = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorPaletteOnly = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorFlatPalette = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorFlatPaletteOnly = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorCustomPalette = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorTogglePalette = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorSelection = null;

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

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateInline = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datetimeInline = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timeInline = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCalendarUi = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateStatic = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePickerOpts = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAltFormat = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNative = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datetimeClock = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $timeHour12 = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datetimeHour12 = null;

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
    private ?string $maskPhone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maskPlate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maskPin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maskDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maskSecure = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maskNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maskPrepareDigits = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maskDefinitions = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signature = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signatureNoUndo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signatureNoClear = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signaturePenColor = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signatureThick = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signatureBackground = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signatureJpeg = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $signatureSvg = null;

    #[ORM\Column(nullable: true)]
    private ?int $slider = null;

    #[ORM\Column(nullable: true)]
    private ?float $sliderFloat = null;

    #[ORM\Column(nullable: true)]
    private ?int $sliderShowInput = null;

    #[ORM\Column(nullable: true)]
    private ?int $sliderConnectUpper = null;

    #[ORM\Column(nullable: true)]
    private ?int $sliderNoTooltip = null;

    #[ORM\Column(nullable: true)]
    private ?int $sliderRtl = null;

    #[ORM\Column(nullable: true)]
    private ?int $sliderVertical = null;

    #[ORM\Column(nullable: true)]
    private ?float $sliderSnap = null;

    #[ORM\Column(nullable: true)]
    private ?int $sliderBehaviour = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugTarget = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugConfirm = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugLocaleEs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugKeepCase = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugKeepDots = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditor = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorBasic = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorPlaceholder = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorFixedHeight = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorAutoresize = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorResizeBoth = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorCustomToolbar = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorStickyToolbar = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorMenubar = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorSelectionQuickbar = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorInsertQuickbar = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorPasteAsText = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorContextMenu = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorReadonly = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorNoStatusbar = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorNoElementPath = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texteditorReducedPlugins = null;

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

    public function getTextRequired(): ?string
    {
        return $this->textRequired;
    }

    public function setTextRequired(string $textRequired): static
    {
        $this->textRequired = $textRequired;

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

    public function getFloatStepHalf(): ?float
    {
        return $this->floatStepHalf;
    }

    public function setFloatStepHalf(?float $floatStepHalf): static
    {
        $this->floatStepHalf = $floatStepHalf;

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

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): static
    {
        $this->file = $file;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

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

    public function getTextareaBounds(): ?string
    {
        return $this->textareaBounds;
    }

    public function setTextareaBounds(?string $textareaBounds): static
    {
        $this->textareaBounds = $textareaBounds;

        return $this;
    }

    public function getTextareaNoAutogrow(): ?string
    {
        return $this->textareaNoAutogrow;
    }

    public function setTextareaNoAutogrow(?string $textareaNoAutogrow): static
    {
        $this->textareaNoAutogrow = $textareaNoAutogrow;

        return $this;
    }

    public function getTextareaStatic(): ?string
    {
        return $this->textareaStatic;
    }

    public function setTextareaStatic(?string $textareaStatic): static
    {
        $this->textareaStatic = $textareaStatic;

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

    public function getChoiceExpanded(): ?UserGender
    {
        return $this->choiceExpanded;
    }

    public function setChoiceExpanded(?UserGender $choiceExpanded): static
    {
        $this->choiceExpanded = $choiceExpanded;

        return $this;
    }

    public function getChoiceExpandedRequired(): ?UserGender
    {
        return $this->choiceExpandedRequired;
    }

    public function setChoiceExpandedRequired(?UserGender $choiceExpandedRequired): static
    {
        $this->choiceExpandedRequired = $choiceExpandedRequired;

        return $this;
    }

    public function getChoiceMultiple(): ?array
    {
        return $this->choiceMultiple;
    }

    public function setChoiceMultiple(?array $choiceMultiple): static
    {
        $this->choiceMultiple = $choiceMultiple;

        return $this;
    }

    public function getChoiceMultipleExpanded(): ?array
    {
        return $this->choiceMultipleExpanded;
    }

    public function setChoiceMultipleExpanded(?array $choiceMultipleExpanded): static
    {
        $this->choiceMultipleExpanded = $choiceMultipleExpanded;

        return $this;
    }

    public function getSelectDefault(): ?string
    {
        return $this->selectDefault;
    }

    public function setSelectDefault(?string $selectDefault): static
    {
        $this->selectDefault = $selectDefault;

        return $this;
    }

    public function getSelectEnum(): ?UserGender
    {
        return $this->selectEnum;
    }

    public function setSelectEnum(?UserGender $selectEnum): static
    {
        $this->selectEnum = $selectEnum;

        return $this;
    }

    public function getSelectRequired(): ?string
    {
        return $this->selectRequired;
    }

    public function setSelectRequired(?string $selectRequired): static
    {
        $this->selectRequired = $selectRequired;

        return $this;
    }

    public function getSelectCreate(): ?string
    {
        return $this->selectCreate;
    }

    public function setSelectCreate(?string $selectCreate): static
    {
        $this->selectCreate = $selectCreate;

        return $this;
    }

    public function getSelectCreateOnBlur(): ?string
    {
        return $this->selectCreateOnBlur;
    }

    public function setSelectCreateOnBlur(?string $selectCreateOnBlur): static
    {
        $this->selectCreateOnBlur = $selectCreateOnBlur;

        return $this;
    }

    public function getSelectHtml(): ?string
    {
        return $this->selectHtml;
    }

    public function setSelectHtml(?string $selectHtml): static
    {
        $this->selectHtml = $selectHtml;

        return $this;
    }

    public function getSelectMultiple(): ?array
    {
        return $this->selectMultiple;
    }

    public function setSelectMultiple(?array $selectMultiple): static
    {
        $this->selectMultiple = $selectMultiple;

        return $this;
    }

    public function getSelectMaxOptions(): ?string
    {
        return $this->selectMaxOptions;
    }

    public function setSelectMaxOptions(?string $selectMaxOptions): static
    {
        $this->selectMaxOptions = $selectMaxOptions;

        return $this;
    }

    public function getSelectMaxItems(): ?array
    {
        return $this->selectMaxItems;
    }

    public function setSelectMaxItems(?array $selectMaxItems): static
    {
        $this->selectMaxItems = $selectMaxItems;

        return $this;
    }

    public function getSelectOnTab(): ?string
    {
        return $this->selectOnTab;
    }

    public function setSelectOnTab(?string $selectOnTab): static
    {
        $this->selectOnTab = $selectOnTab;

        return $this;
    }

    public function getSelectDiacritics(): ?string
    {
        return $this->selectDiacritics;
    }

    public function setSelectDiacritics(?string $selectDiacritics): static
    {
        $this->selectDiacritics = $selectDiacritics;

        return $this;
    }

    public function getSelectNoClear(): ?string
    {
        return $this->selectNoClear;
    }

    public function setSelectNoClear(?string $selectNoClear): static
    {
        $this->selectNoClear = $selectNoClear;

        return $this;
    }

    public function getSelectNoDropdown(): ?string
    {
        return $this->selectNoDropdown;
    }

    public function setSelectNoDropdown(?string $selectNoDropdown): static
    {
        $this->selectNoDropdown = $selectNoDropdown;

        return $this;
    }

    public function getSelectCloseAfterSelect(): ?array
    {
        return $this->selectCloseAfterSelect;
    }

    public function setSelectCloseAfterSelect(?array $selectCloseAfterSelect): static
    {
        $this->selectCloseAfterSelect = $selectCloseAfterSelect;

        return $this;
    }

    public function getSelectHideSelected(): ?array
    {
        return $this->selectHideSelected;
    }

    public function setSelectHideSelected(?array $selectHideSelected): static
    {
        $this->selectHideSelected = $selectHideSelected;

        return $this;
    }

    public function getSelectCheckbox(): ?array
    {
        return $this->selectCheckbox;
    }

    public function setSelectCheckbox(?array $selectCheckbox): static
    {
        $this->selectCheckbox = $selectCheckbox;

        return $this;
    }

    public function getSelectNoRemove(): ?array
    {
        return $this->selectNoRemove;
    }

    public function setSelectNoRemove(?array $selectNoRemove): static
    {
        $this->selectNoRemove = $selectNoRemove;

        return $this;
    }

    public function getSelectDuplicates(): ?array
    {
        return $this->selectDuplicates;
    }

    public function setSelectDuplicates(?array $selectDuplicates): static
    {
        $this->selectDuplicates = $selectDuplicates;

        return $this;
    }

    public function getSelectRole(): ?Role
    {
        return $this->selectRole;
    }

    public function setSelectRole(?Role $selectRole): static
    {
        $this->selectRole = $selectRole;

        return $this;
    }

    public function getSelectUser(): ?User
    {
        return $this->selectUser;
    }

    public function setSelectUser(?User $selectUser): static
    {
        $this->selectUser = $selectUser;

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

    public function getCodeeditorPhpGithub(): ?string
    {
        return $this->codeeditorPhpGithub;
    }

    public function setCodeeditorPhpGithub(?string $codeeditorPhpGithub): static
    {
        $this->codeeditorPhpGithub = $codeeditorPhpGithub;

        return $this;
    }

    public function getCodeeditorPlaceholder(): ?string
    {
        return $this->codeeditorPlaceholder;
    }

    public function setCodeeditorPlaceholder(?string $codeeditorPlaceholder): static
    {
        $this->codeeditorPlaceholder = $codeeditorPlaceholder;

        return $this;
    }

    public function getCodeeditorBounds(): ?string
    {
        return $this->codeeditorBounds;
    }

    public function setCodeeditorBounds(?string $codeeditorBounds): static
    {
        $this->codeeditorBounds = $codeeditorBounds;

        return $this;
    }

    public function getCodeeditorIndentSpaces(): ?string
    {
        return $this->codeeditorIndentSpaces;
    }

    public function setCodeeditorIndentSpaces(?string $codeeditorIndentSpaces): static
    {
        $this->codeeditorIndentSpaces = $codeeditorIndentSpaces;

        return $this;
    }

    public function getCodeeditorWrap(): ?string
    {
        return $this->codeeditorWrap;
    }

    public function setCodeeditorWrap(?string $codeeditorWrap): static
    {
        $this->codeeditorWrap = $codeeditorWrap;

        return $this;
    }

    public function getCodeeditorPrintMargin(): ?string
    {
        return $this->codeeditorPrintMargin;
    }

    public function setCodeeditorPrintMargin(?string $codeeditorPrintMargin): static
    {
        $this->codeeditorPrintMargin = $codeeditorPrintMargin;

        return $this;
    }

    public function getCodeeditorMinimalUi(): ?string
    {
        return $this->codeeditorMinimalUi;
    }

    public function setCodeeditorMinimalUi(?string $codeeditorMinimalUi): static
    {
        $this->codeeditorMinimalUi = $codeeditorMinimalUi;

        return $this;
    }

    public function getCodeeditorReadonly(): ?string
    {
        return $this->codeeditorReadonly;
    }

    public function setCodeeditorReadonly(?string $codeeditorReadonly): static
    {
        $this->codeeditorReadonly = $codeeditorReadonly;

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

    public function getCollectionNoAdd(): ?array
    {
        return $this->collectionNoAdd;
    }

    public function setCollectionNoAdd(?array $collectionNoAdd): static
    {
        $this->collectionNoAdd = $collectionNoAdd;

        return $this;
    }

    public function getCollectionNoDelete(): ?array
    {
        return $this->collectionNoDelete;
    }

    public function setCollectionNoDelete(?array $collectionNoDelete): static
    {
        $this->collectionNoDelete = $collectionNoDelete;

        return $this;
    }

    public function getCollectionLabels(): ?array
    {
        return $this->collectionLabels;
    }

    public function setCollectionLabels(?array $collectionLabels): static
    {
        $this->collectionLabels = $collectionLabels;

        return $this;
    }

    public function getCollectionPlugins(): ?array
    {
        return $this->collectionPlugins;
    }

    public function setCollectionPlugins(?array $collectionPlugins): static
    {
        $this->collectionPlugins = $collectionPlugins;

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

    public function getColorFlat(): ?string
    {
        return $this->colorFlat;
    }

    public function setColorFlat(?string $colorFlat): static
    {
        $this->colorFlat = $colorFlat;

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

    public function getColorButtons(): ?string
    {
        return $this->colorButtons;
    }

    public function setColorButtons(?string $colorButtons): static
    {
        $this->colorButtons = $colorButtons;

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

    public function getColorFlatPalette(): ?string
    {
        return $this->colorFlatPalette;
    }

    public function setColorFlatPalette(?string $colorFlatPalette): static
    {
        $this->colorFlatPalette = $colorFlatPalette;

        return $this;
    }

    public function getColorFlatPaletteOnly(): ?string
    {
        return $this->colorFlatPaletteOnly;
    }

    public function setColorFlatPaletteOnly(?string $colorFlatPaletteOnly): static
    {
        $this->colorFlatPaletteOnly = $colorFlatPaletteOnly;

        return $this;
    }

    public function getColorCustomPalette(): ?string
    {
        return $this->colorCustomPalette;
    }

    public function setColorCustomPalette(?string $colorCustomPalette): static
    {
        $this->colorCustomPalette = $colorCustomPalette;

        return $this;
    }

    public function getColorTogglePalette(): ?string
    {
        return $this->colorTogglePalette;
    }

    public function setColorTogglePalette(?string $colorTogglePalette): static
    {
        $this->colorTogglePalette = $colorTogglePalette;

        return $this;
    }

    public function getColorSelection(): ?string
    {
        return $this->colorSelection;
    }

    public function setColorSelection(?string $colorSelection): static
    {
        $this->colorSelection = $colorSelection;

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

    public function getDateCalendarUi(): ?\DateTimeInterface
    {
        return $this->dateCalendarUi;
    }

    public function setDateCalendarUi(?\DateTimeInterface $dateCalendarUi): static
    {
        $this->dateCalendarUi = $dateCalendarUi;

        return $this;
    }

    public function getDateStatic(): ?\DateTimeInterface
    {
        return $this->dateStatic;
    }

    public function setDateStatic(?\DateTimeInterface $dateStatic): static
    {
        $this->dateStatic = $dateStatic;

        return $this;
    }

    public function getDatePickerOpts(): ?\DateTimeInterface
    {
        return $this->datePickerOpts;
    }

    public function setDatePickerOpts(?\DateTimeInterface $datePickerOpts): static
    {
        $this->datePickerOpts = $datePickerOpts;

        return $this;
    }

    public function getDateAltFormat(): ?\DateTimeInterface
    {
        return $this->dateAltFormat;
    }

    public function setDateAltFormat(?\DateTimeInterface $dateAltFormat): static
    {
        $this->dateAltFormat = $dateAltFormat;

        return $this;
    }

    public function getDateNative(): ?\DateTimeInterface
    {
        return $this->dateNative;
    }

    public function setDateNative(?\DateTimeInterface $dateNative): static
    {
        $this->dateNative = $dateNative;

        return $this;
    }

    public function getDatetimeClock(): ?\DateTimeInterface
    {
        return $this->datetimeClock;
    }

    public function setDatetimeClock(?\DateTimeInterface $datetimeClock): static
    {
        $this->datetimeClock = $datetimeClock;

        return $this;
    }

    public function getTimeHour12(): ?\DateTimeInterface
    {
        return $this->timeHour12;
    }

    public function setTimeHour12(?\DateTimeInterface $timeHour12): static
    {
        $this->timeHour12 = $timeHour12;

        return $this;
    }

    public function getDatetimeHour12(): ?\DateTimeInterface
    {
        return $this->datetimeHour12;
    }

    public function setDatetimeHour12(?\DateTimeInterface $datetimeHour12): static
    {
        $this->datetimeHour12 = $datetimeHour12;

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

    public function getMaskPhone(): ?string
    {
        return $this->maskPhone;
    }

    public function setMaskPhone(?string $maskPhone): static
    {
        $this->maskPhone = $maskPhone;

        return $this;
    }

    public function getMaskPlate(): ?string
    {
        return $this->maskPlate;
    }

    public function setMaskPlate(?string $maskPlate): static
    {
        $this->maskPlate = $maskPlate;

        return $this;
    }

    public function getMaskPin(): ?string
    {
        return $this->maskPin;
    }

    public function setMaskPin(?string $maskPin): static
    {
        $this->maskPin = $maskPin;

        return $this;
    }

    public function getMaskDate(): ?string
    {
        return $this->maskDate;
    }

    public function setMaskDate(?string $maskDate): static
    {
        $this->maskDate = $maskDate;

        return $this;
    }

    public function getMaskSecure(): ?string
    {
        return $this->maskSecure;
    }

    public function setMaskSecure(?string $maskSecure): static
    {
        $this->maskSecure = $maskSecure;

        return $this;
    }

    public function getMaskNumber(): ?string
    {
        return $this->maskNumber;
    }

    public function setMaskNumber(?string $maskNumber): static
    {
        $this->maskNumber = $maskNumber;

        return $this;
    }

    public function getMaskPrepareDigits(): ?string
    {
        return $this->maskPrepareDigits;
    }

    public function setMaskPrepareDigits(?string $maskPrepareDigits): static
    {
        $this->maskPrepareDigits = $maskPrepareDigits;

        return $this;
    }

    public function getMaskDefinitions(): ?string
    {
        return $this->maskDefinitions;
    }

    public function setMaskDefinitions(?string $maskDefinitions): static
    {
        $this->maskDefinitions = $maskDefinitions;

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

    public function getSignatureNoUndo(): ?string
    {
        return $this->signatureNoUndo;
    }

    public function setSignatureNoUndo(?string $signatureNoUndo): static
    {
        $this->signatureNoUndo = $signatureNoUndo;

        return $this;
    }

    public function getSignatureNoClear(): ?string
    {
        return $this->signatureNoClear;
    }

    public function setSignatureNoClear(?string $signatureNoClear): static
    {
        $this->signatureNoClear = $signatureNoClear;

        return $this;
    }

    public function getSignaturePenColor(): ?string
    {
        return $this->signaturePenColor;
    }

    public function setSignaturePenColor(?string $signaturePenColor): static
    {
        $this->signaturePenColor = $signaturePenColor;

        return $this;
    }

    public function getSignatureThick(): ?string
    {
        return $this->signatureThick;
    }

    public function setSignatureThick(?string $signatureThick): static
    {
        $this->signatureThick = $signatureThick;

        return $this;
    }

    public function getSignatureBackground(): ?string
    {
        return $this->signatureBackground;
    }

    public function setSignatureBackground(?string $signatureBackground): static
    {
        $this->signatureBackground = $signatureBackground;

        return $this;
    }

    public function getSignatureJpeg(): ?string
    {
        return $this->signatureJpeg;
    }

    public function setSignatureJpeg(?string $signatureJpeg): static
    {
        $this->signatureJpeg = $signatureJpeg;

        return $this;
    }

    public function getSignatureSvg(): ?string
    {
        return $this->signatureSvg;
    }

    public function setSignatureSvg(?string $signatureSvg): static
    {
        $this->signatureSvg = $signatureSvg;

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

    public function getSliderShowInput(): ?int
    {
        return $this->sliderShowInput;
    }

    public function setSliderShowInput(?int $sliderShowInput): static
    {
        $this->sliderShowInput = $sliderShowInput;

        return $this;
    }

    public function getSliderConnectUpper(): ?int
    {
        return $this->sliderConnectUpper;
    }

    public function setSliderConnectUpper(?int $sliderConnectUpper): static
    {
        $this->sliderConnectUpper = $sliderConnectUpper;

        return $this;
    }

    public function getSliderNoTooltip(): ?int
    {
        return $this->sliderNoTooltip;
    }

    public function setSliderNoTooltip(?int $sliderNoTooltip): static
    {
        $this->sliderNoTooltip = $sliderNoTooltip;

        return $this;
    }

    public function getSliderRtl(): ?int
    {
        return $this->sliderRtl;
    }

    public function setSliderRtl(?int $sliderRtl): static
    {
        $this->sliderRtl = $sliderRtl;

        return $this;
    }

    public function getSliderVertical(): ?int
    {
        return $this->sliderVertical;
    }

    public function setSliderVertical(?int $sliderVertical): static
    {
        $this->sliderVertical = $sliderVertical;

        return $this;
    }

    public function getSliderSnap(): ?float
    {
        return $this->sliderSnap;
    }

    public function setSliderSnap(?float $sliderSnap): static
    {
        $this->sliderSnap = $sliderSnap;

        return $this;
    }

    public function getSliderBehaviour(): ?int
    {
        return $this->sliderBehaviour;
    }

    public function setSliderBehaviour(?int $sliderBehaviour): static
    {
        $this->sliderBehaviour = $sliderBehaviour;

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

    public function getSlugConfirm(): ?string
    {
        return $this->slugConfirm;
    }

    public function setSlugConfirm(?string $slugConfirm): static
    {
        $this->slugConfirm = $slugConfirm;

        return $this;
    }

    public function getSlugLocaleEs(): ?string
    {
        return $this->slugLocaleEs;
    }

    public function setSlugLocaleEs(?string $slugLocaleEs): static
    {
        $this->slugLocaleEs = $slugLocaleEs;

        return $this;
    }

    public function getSlugKeepCase(): ?string
    {
        return $this->slugKeepCase;
    }

    public function setSlugKeepCase(?string $slugKeepCase): static
    {
        $this->slugKeepCase = $slugKeepCase;

        return $this;
    }

    public function getSlugKeepDots(): ?string
    {
        return $this->slugKeepDots;
    }

    public function setSlugKeepDots(?string $slugKeepDots): static
    {
        $this->slugKeepDots = $slugKeepDots;

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

    public function getTexteditorBasic(): ?string
    {
        return $this->texteditorBasic;
    }

    public function setTexteditorBasic(?string $texteditorBasic): static
    {
        $this->texteditorBasic = $texteditorBasic;

        return $this;
    }

    public function getTexteditorPlaceholder(): ?string
    {
        return $this->texteditorPlaceholder;
    }

    public function setTexteditorPlaceholder(?string $texteditorPlaceholder): static
    {
        $this->texteditorPlaceholder = $texteditorPlaceholder;

        return $this;
    }

    public function getTexteditorFixedHeight(): ?string
    {
        return $this->texteditorFixedHeight;
    }

    public function setTexteditorFixedHeight(?string $texteditorFixedHeight): static
    {
        $this->texteditorFixedHeight = $texteditorFixedHeight;

        return $this;
    }

    public function getTexteditorAutoresize(): ?string
    {
        return $this->texteditorAutoresize;
    }

    public function setTexteditorAutoresize(?string $texteditorAutoresize): static
    {
        $this->texteditorAutoresize = $texteditorAutoresize;

        return $this;
    }

    public function getTexteditorResizeBoth(): ?string
    {
        return $this->texteditorResizeBoth;
    }

    public function setTexteditorResizeBoth(?string $texteditorResizeBoth): static
    {
        $this->texteditorResizeBoth = $texteditorResizeBoth;

        return $this;
    }

    public function getTexteditorCustomToolbar(): ?string
    {
        return $this->texteditorCustomToolbar;
    }

    public function setTexteditorCustomToolbar(?string $texteditorCustomToolbar): static
    {
        $this->texteditorCustomToolbar = $texteditorCustomToolbar;

        return $this;
    }

    public function getTexteditorStickyToolbar(): ?string
    {
        return $this->texteditorStickyToolbar;
    }

    public function setTexteditorStickyToolbar(?string $texteditorStickyToolbar): static
    {
        $this->texteditorStickyToolbar = $texteditorStickyToolbar;

        return $this;
    }

    public function getTexteditorMenubar(): ?string
    {
        return $this->texteditorMenubar;
    }

    public function setTexteditorMenubar(?string $texteditorMenubar): static
    {
        $this->texteditorMenubar = $texteditorMenubar;

        return $this;
    }

    public function getTexteditorSelectionQuickbar(): ?string
    {
        return $this->texteditorSelectionQuickbar;
    }

    public function setTexteditorSelectionQuickbar(?string $texteditorSelectionQuickbar): static
    {
        $this->texteditorSelectionQuickbar = $texteditorSelectionQuickbar;

        return $this;
    }

    public function getTexteditorInsertQuickbar(): ?string
    {
        return $this->texteditorInsertQuickbar;
    }

    public function setTexteditorInsertQuickbar(?string $texteditorInsertQuickbar): static
    {
        $this->texteditorInsertQuickbar = $texteditorInsertQuickbar;

        return $this;
    }

    public function getTexteditorPasteAsText(): ?string
    {
        return $this->texteditorPasteAsText;
    }

    public function setTexteditorPasteAsText(?string $texteditorPasteAsText): static
    {
        $this->texteditorPasteAsText = $texteditorPasteAsText;

        return $this;
    }

    public function getTexteditorContextMenu(): ?string
    {
        return $this->texteditorContextMenu;
    }

    public function setTexteditorContextMenu(?string $texteditorContextMenu): static
    {
        $this->texteditorContextMenu = $texteditorContextMenu;

        return $this;
    }

    public function getTexteditorReadonly(): ?string
    {
        return $this->texteditorReadonly;
    }

    public function setTexteditorReadonly(?string $texteditorReadonly): static
    {
        $this->texteditorReadonly = $texteditorReadonly;

        return $this;
    }

    public function getTexteditorNoStatusbar(): ?string
    {
        return $this->texteditorNoStatusbar;
    }

    public function setTexteditorNoStatusbar(?string $texteditorNoStatusbar): static
    {
        $this->texteditorNoStatusbar = $texteditorNoStatusbar;

        return $this;
    }

    public function getTexteditorNoElementPath(): ?string
    {
        return $this->texteditorNoElementPath;
    }

    public function setTexteditorNoElementPath(?string $texteditorNoElementPath): static
    {
        $this->texteditorNoElementPath = $texteditorNoElementPath;

        return $this;
    }

    public function getTexteditorReducedPlugins(): ?string
    {
        return $this->texteditorReducedPlugins;
    }

    public function setTexteditorReducedPlugins(?string $texteditorReducedPlugins): static
    {
        $this->texteditorReducedPlugins = $texteditorReducedPlugins;

        return $this;
    }
}
