<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField as EasyField;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\File;

class FileField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_ACCEPT = 'accept';
    public const OPTION_FILE_CONSTRAINTS = 'fileConstraints';
    public const DEFAULT_DIR = 'media';
    public const DEFAULT_TEMPLATE_PATH = 'field/file.html.twig';

    public static function new(string $propertyName, ?string $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->initField($field->innerField);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
        $this->dto->setAssets(new AssetsDto());
        $this->addAssetMapperEntries(Asset::new('form-type-file')->onlyOnForms());
        $this->setDir(self::DEFAULT_DIR);
        $this->setTemplatePath(self::DEFAULT_TEMPLATE_PATH);
        $this->setCustomOption(self::OPTION_FILE_CONSTRAINTS, [new File()]);
    }

    public function setAccept(?string $filetype): self
    {
        $this->setHtmlAttribute(self::OPTION_ACCEPT, $filetype);

        return $this;
    }

    public function setDir(string $dir): self
    {
        $this->innerField->setBasePath($dir);
        $this->innerField->setUploadDir('public/'.$dir);

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

    /**
     * @param string|\Closure $pattern
     */
    public function setUploadedFileNamePattern($pattern): self
    {
        $this->innerField->setUploadedFileNamePattern($pattern);

        return $this;
    }

    /**
     * @param Constraint|array<Constraint> $constraints
     */
    public function setFileConstraints($constraints): self
    {
        $this->innerField->setFileConstraints($constraints);

        return $this;
    }
}
