<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use Arkounay\Bundle\UxMediaBundle\Form\UxMediaType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * Arkounay `UxMediaType` on an EasyAdmin `TextField` shell: Artgris `conf` keys, crop/zoom options, and `field/media.html.twig`.
 */
class MediaField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_CONF = 'conf';
    public const OPTION_DISPLAY_FILE_MANAGER = 'display_file_manager';
    public const OPTION_DISPLAY_CLEAR_BUTTON = 'display_clear_button';
    public const OPTION_DISPLAY_TREE = 'tree';
    public const OPTION_ALLOW_CROP = 'allow_crop';

    public const OPTION_CROP_OPTIONS = 'crop_options';
    public const OPTION_CROP_DISPLAY_CROP_DATA = 'display_crop_data';
    public const OPTION_CROP_ALLOW_FLIP = 'allow_flip';
    public const OPTION_CROP_ALLOW_ROTATION = 'allow_rotation';
    public const OPTION_CROP_RATIO = 'ratio';

    public const OPTION_ALLOW_ZOOM = 'allow_zoom';
    public const OPTION_SIZE_INDEX = 'size_index';
    public const OPTION_SIZE_DETAIL = 'size_detail';

    public const DEFAULT_CONF = 'public_all';
    public const DEFAULT_DISPLAY_FILE_MANAGER = true;
    public const DEFAULT_DISPLAY_TREE = false;
    public const DEFAULT_ALLOW_CROP = false;
    public const DEFAULT_RATIO = false;
    public const DEFAULT_ALLOW_ZOOM = true;
    public const DEFAULT_SIZE_INDEX = 'md';
    public const DEFAULT_SIZE_DETAIL = 'xl';

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->initField($field->innerField);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
        $this->setFormType(UxMediaType::class);
        $this->setTemplatePath('field/media.html.twig');
        $this->setConf(self::DEFAULT_CONF);
        $this->displayFileManager(self::DEFAULT_DISPLAY_FILE_MANAGER);
        $this->displayTree(self::DEFAULT_DISPLAY_TREE);
        $this->allowCrop(self::DEFAULT_ALLOW_CROP);
        $this->allowZoom(self::DEFAULT_ALLOW_ZOOM);
        $this->setSizeIndex(self::DEFAULT_SIZE_INDEX);
        $this->setSizeDetail(self::DEFAULT_SIZE_DETAIL);
    }

    /** Artgris file-manager config key (default `public_all`). */
    public function setConf(string $conf): self
    {
        $this->setFormTypeOption(self::OPTION_CONF, $conf);

        return $this;
    }

    /** Show the button that opens the file manager. Default on. */
    public function displayFileManager(bool $display = true): self
    {
        $this->setFormTypeOption(self::OPTION_DISPLAY_FILE_MANAGER, $display);

        return $this;
    }

    /** Show the button that clears the selected file. */
    public function displayClearButton(bool $display = true): self
    {
        $this->setFormTypeOption(self::OPTION_DISPLAY_CLEAR_BUTTON, $display);

        return $this;
    }

    /** Show the folder tree in the file manager. Default off. */
    public function displayTree(bool $display = true): self
    {
        $this->setFormTypeOption(self::OPTION_DISPLAY_TREE, $display);

        return $this;
    }

    /** Allow cropping the selected image. Default off. */
    public function allowCrop(bool $allow = true): self
    {
        $this->setFormTypeOption(self::OPTION_ALLOW_CROP, $allow);

        return $this;
    }

    /** Show the crop coordinates. Also turns cropping on. */
    public function displayCropData(bool $display = true): self
    {
        $this->allowCrop();
        $this->setFormTypeOption(self::OPTION_CROP_OPTIONS.'.'.self::OPTION_CROP_DISPLAY_CROP_DATA, $display);

        return $this;
    }

    /** Allow flipping the image in the cropper. Also turns cropping on. */
    public function allowFlip(bool $allow = true): self
    {
        $this->allowCrop();
        $this->setFormTypeOption(self::OPTION_CROP_OPTIONS.'.'.self::OPTION_CROP_ALLOW_FLIP, $allow);

        return $this;
    }

    /** Allow rotating the image in the cropper. Also turns cropping on. */
    public function allowRotation(bool $allow = true): self
    {
        $this->allowCrop();
        $this->setFormTypeOption(self::OPTION_CROP_OPTIONS.'.'.self::OPTION_CROP_ALLOW_ROTATION, $allow);

        return $this;
    }

    /** Lock the crop aspect ratio. `false` (default) leaves the crop free. Also turns cropping on. */
    public function setRatio(bool $ratio = self::DEFAULT_RATIO): self
    {
        $this->allowCrop();
        $this->setFormTypeOption(self::OPTION_CROP_OPTIONS.'.'.self::OPTION_CROP_RATIO, $ratio);

        return $this;
    }

    /** Allow zooming the preview. Default on. */
    public function allowZoom(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_ZOOM, $allow);

        return $this;
    }

    /** Thumbnail size on the index (`sm`, `md`, `lg`, `xl`). Default `md`. */
    public function setSizeIndex(string $size = self::DEFAULT_SIZE_INDEX): self
    {
        $this->setCustomOption(self::OPTION_SIZE_INDEX, $size);

        return $this;
    }

    /** Preview size on detail (`sm`, `md`, `lg`, `xl`). Default `xl`. */
    public function setSizeDetail(string $size = self::DEFAULT_SIZE_DETAIL): self
    {
        $this->setCustomOption(self::OPTION_SIZE_DETAIL, $size);

        return $this;
    }
}
