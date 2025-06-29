<?php

namespace App\Field;

use Symfony\Component\Form\AbstractType;

class FieldGenerator extends AbstractType
{
    public static function tab(string $name)
    {
        return FormField::tab($name);
    }

    public static function panel(string $name)
    {
        return FormField::panel($name);
    }

    public static function row(string $breakpointName = '')
    {
        return FormField::row($breakpointName);
    }

    public static function col(int|string $cols = 'col')
    {
        return FormField::col($cols);
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

    public static function checkbox(string $name)
    {
        return BooleanField::new($name);
    }

    public static function switch(string $name)
    {
        return BooleanField::new($name)
            ->switch();
    }

    public static function email(string $name)
    {
        return EmailField::new($name);
    }

    public static function phone(string $name)
    {
        return TelephoneField::new($name);
    }

    public static function url(string $name)
    {
        return UrlField::new($name);
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
        return TimezoneField::new($name);
    }

    public static function password(string $name)
    {
        return PasswordField::new($name);
    }

    public static function repeat(string $name)
    {
        return RepeatField::new($name);
    }

    public static function integer(string $name)
    {
        return IntegerField::new($name);
    }

    public static function float(string $name)
    {
        return FloatField::new($name);
    }

    public static function percent(string $name)
    {
        return PercentField::new($name);
    }

    public static function money(string $name)
    {
        return MoneyField::new($name);
    }

    public static function color(string $name)
    {
        return ColorField::new($name);
    }

    public static function array(string $name)
    {
        return ArrayField::new($name);
    }

    public static function collection(string $name)
    {
        return CollectionField::new($name);
    }

    public static function association(string $name)
    {
        return AssociationField::new($name);
    }

    public static function file(string $name)
    {
        return FileField::new($name);
    }

    public static function signature(string $name)
    {
        return SignatureField::new($name);
    }
}
