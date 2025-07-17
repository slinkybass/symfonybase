<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField as EasyField;

class FileField
{
    use FieldTrait;

    public const OPTION_ACCEPT = 'accept';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->setCssClass('field-file')
            ->setUploadDir('public/media')
            ->setDefaultColumns(12);

        return $instance;
    }

    public function setAccept(?string $filetype): self
    {
        $this->setHtmlAttribute(self::OPTION_ACCEPT, $filetype);

        return $this;
    }

    public function setBasePath(string $path): self
    {
        $this->field->setBasePath($path);

        return $this;
    }

    public function setUploadDir(string $dir): self
    {
        $this->field->setUploadDir($dir);

        return $this;
    }

    public function setUploadedFileNamePattern($pattern): self
    {
        $this->field->setUploadedFileNamePattern($pattern);

        return $this;
    }

    public function setFileConstraints($constraints): self
    {
        $this->field->setFileConstraints($constraints);

        return $this;
    }
}
