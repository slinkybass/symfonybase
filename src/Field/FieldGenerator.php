<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimezoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Form\AbstractType;

class FieldGenerator extends AbstractType
{
    public static function tab(string $name)
    {
        return FormField::addTab($name);
    }

    public static function panel(string $name)
    {
        return FormField::addPanel($name);
    }

    public static function row(string $breakpointName = '')
    {
        return FormField::addRow($breakpointName);
    }

    public static function col(int|string $cols = 'col')
    {
        return FormField::addColumn($cols);
    }

    public static function field(string $name)
    {
        return Field::new($name);
    }

    public static function id(string $name)
    {
        return IdField::new($name);
    }

    public static function text(string $name)
    {
        return TextField::new($name);
    }

    public static function hidden(string $name)
    {
        return HiddenField::new($name);
    }

    public static function slug(string $name)
    {
        return SlugField::new($name);
    }

    public static function textarea(string $name)
    {
        return TextareaField::new($name);
    }

    public static function texteditor(string $name)
    {
        return TextEditorField::new($name);
    }

    public static function codeeditor(string $name)
    {
        return CodeEditorField::new($name);
    }

    public static function choice(string $name)
    {
        return ChoiceField::new($name);
    }

    public static function choiceMutiple(string $name)
    {
        return ChoiceField::new($name)
            ->multiple()
            ->setColumns(12);
    }

    public static function choiceExpanded(string $name)
    {
        return ChoiceField::new($name)
            ->expanded();
    }

    public static function choiceExpandedMutiple(string $name)
    {
        return ChoiceField::new($name)
            ->expanded()
            ->multiple();
    }

    public static function checkbox(string $name)
    {
        return BooleanField::new($name)
            ->renderAsSwitch(false)
            ->setColumns(12);
    }

    public static function switch(string $name)
    {
        return BooleanField::new($name)
            ->setColumns(12);
    }

    public static function email(string $name)
    {
        return EmailField::new($name)
            ->setColumns(12);
    }

    public static function phone(string $name)
    {
        return TelephoneField::new($name)
            ->setColumns(12);
    }

    public static function url(string $name)
    {
        return UrlField::new($name)
            ->setColumns(12);
    }

    public static function date(string $name)
    {
        return DateField::new($name);
    }

    public static function datetime(string $name)
    {
        return DateTimeField::new($name);
    }

    public static function time(string $name)
    {
        return TimeField::new($name);
    }

    public static function timezone(string $name)
    {
        return TimezoneField::new($name)
            ->setColumns(12);
    }

    public static function password(string $name)
    {
        return PasswordField::new($name);
    }

    public static function repeat(string $name)
    {
        return RepeatField::new($name);
    }

    public static function float(string $name)
    {
        return NumberField::new($name)
            ->setNumDecimals(2)
            ->setFormTypeOption('html5', true)
            ->setColumns(12);
    }

    public static function integer(string $name)
    {
        return IntegerField::new($name)
            ->setColumns(12);
    }

    public static function percent(string $name)
    {
        return PercentField::new($name)
            ->setColumns(12);
    }

    public static function money(string $name)
    {
        return MoneyField::new($name)
            ->setCurrency('EUR')
            ->setStoredAsCents(false)
            ->setColumns(12);
    }

    public static function color(string $name)
    {
        return ColorField::new($name)
            ->setColumns(12);
    }

    public static function array(string $name)
    {
        return ArrayField::new($name)
            ->setColumns(12);
    }

    public static function collection(string $name)
    {
        return CollectionField::new($name)
            ->setColumns(12);
    }

    public static function association(string $name)
    {
        return AssociationField::new($name)
            ->setColumns(12);
    }

    public static function image(string $name)
    {
        return ImageField::new($name)
            ->setColumns(12);
    }

    public static function avatar(string $name)
    {
        return AvatarField::new($name)
            ->setColumns(12);
    }
}
