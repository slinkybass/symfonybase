<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField as EasyField;

class ImageField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public static function new(string $propertyName, $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->innerField->getAsDto()->setAssets(new AssetsDto());
        $field->initField($field->innerField);
        $field
            ->setDir('media');

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
    }

    public function setAccept(?string $filetype): self
    {
        $this->setHtmlAttribute(FileField::OPTION_ACCEPT, $filetype);

        return $this;
    }

    public function setDir(string $dir): self
    {
        $this->innerField->setBasePath($dir);
        $this->innerField->setUploadDir('public/' . $dir);

        return $this;
    }

    public function setBasePath(string $path): self
    {
        $this->innerField->setBasePath($path);

        return $this;
    }

    public function setUploadDir(string $dir): self
    {
        $this->innerField->setUploadDir($dir);

        return $this;
    }

    public function setUploadedFileNamePattern($pattern): self
    {
        $this->innerField->setUploadedFileNamePattern($pattern);

        return $this;
    }

    public function setFileConstraints($constraints): self
    {
        $this->innerField->setFileConstraints($constraints);

        return $this;
    }
}
