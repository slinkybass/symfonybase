<?php

namespace App\Field;

use Arkounay\Bundle\UxMediaBundle\Form\UxMediaType;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField as EasyField;

class MediaField
{
    use FieldTrait;

    public const OPTION_CONF = 'conf';
    public const OPTION_DISPLAY_TREE = 'tree';
    public const OPTION_DISPLAY_FILE_MANAGER = 'display_file_manager';
    public const OPTION_DISPLAY_CLEAR_BUTTON = 'display_clear_button';
    public const OPTION_ALLOW_CROP = 'allow_crop';

    public const OPTION_CROP_OPTIONS = 'crop_options';
    public const OPTION_CROP_DISPLAY_CROP_DATA = 'display_crop_data';
    public const OPTION_CROP_ALLOW_FLIP = 'allow_flip';
    public const OPTION_CROP_ALLOW_ROTATION = 'allow_rotation';
    public const OPTION_CROP_RATIO = 'ratio';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->setFormType(UxMediaType::class)
			->setTemplatePath('field/media.html.twig')
            ->conf()
            ->displayTree(false)
            ->allowCrop(false)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function conf(string $conf = 'public_all'): self
    {
        $this->setFormTypeOption(self::OPTION_CONF, $conf);

        return $this;
    }

    public function displayTree(bool $val = true): self
    {
        $this->setFormTypeOption(self::OPTION_DISPLAY_TREE, $val);

        return $this;
    }

    public function displayFileManager(bool $val = true): self
    {
        $this->setFormTypeOption(self::OPTION_DISPLAY_FILE_MANAGER, $val);

        return $this;
    }

    public function displayClearButton(bool $val = true): self
    {
        $this->setFormTypeOption(self::OPTION_DISPLAY_CLEAR_BUTTON, $val);

        return $this;
    }

    public function allowCrop(bool $val = true): self
    {
        $this->setFormTypeOption(self::OPTION_ALLOW_CROP, $val);

        return $this;
    }

    public function displayCropData(bool $val = true): self
    {
        $this->allowCrop();
        $this->setFormTypeOption(self::OPTION_CROP_OPTIONS . '.' . self::OPTION_CROP_DISPLAY_CROP_DATA, $val);

        return $this;
    }

    public function allowFlip(bool $val = true): self
    {
        $this->allowCrop();
        $this->setFormTypeOption(self::OPTION_CROP_OPTIONS . '.' . self::OPTION_CROP_ALLOW_FLIP, $val);

        return $this;
    }

    public function allowRotation(bool $val = true): self
    {
        $this->allowCrop();
        $this->setFormTypeOption(self::OPTION_CROP_OPTIONS . '.' . self::OPTION_CROP_ALLOW_ROTATION, $val);

        return $this;
    }

    public function ratio($val = false): self
    {
        $this->allowCrop();
        $this->setFormTypeOption(self::OPTION_CROP_OPTIONS . '.' . self::OPTION_CROP_RATIO, $val);

        return $this;
    }
}
