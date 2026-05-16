<?php

namespace App\Field;

use Symfony\Component\Form\AbstractType;

/**
 * Static factories for EasyAdmin-style field objects used from CRUD controllers and `App\Form\FormGenerator`.
 */
class FieldGenerator extends AbstractType
{
    public static function tab(string $name): FormField
    {
        return FormField::tab($name);
    }

    public static function panel(string $name): FormField
    {
        return FormField::panel($name);
    }

    public static function row(string $breakpointName = ''): FormField
    {
        return FormField::row($breakpointName);
    }

    public static function col(int|string $cols = 'col'): FormField
    {
        return FormField::col($cols);
    }

    public static function field(string $name): Field
    {
        return Field::new($name);
    }

    public static function id(string $name): IdField
    {
        return IdField::new($name);
    }

    public static function text(string $name): TextField
    {
        return TextField::new($name);
    }

    public static function mask(string $name): MaskField
    {
        return MaskField::new($name);
    }

    public static function hidden(string $name): HiddenField
    {
        return HiddenField::new($name);
    }

    public static function slug(string $name): SlugField
    {
        return SlugField::new($name);
    }

    public static function textarea(string $name): TextareaField
    {
        return TextareaField::new($name);
    }

    public static function texteditor(string $name): TextEditorField
    {
        return TextEditorField::new($name);
    }

    public static function codeeditor(string $name): CodeEditorField
    {
        return CodeEditorField::new($name);
    }

    public static function choice(string $name): ChoiceField
    {
        return ChoiceField::new($name);
    }

    public static function enum(string $name): EnumField
    {
        return EnumField::new($name);
    }

    public static function checkbox(string $name): BooleanField
    {
        return BooleanField::new($name);
    }

    public static function switch(string $name): BooleanField
    {
        return BooleanField::new($name)->isSwitch();
    }

    public static function email(string $name): EmailField
    {
        return EmailField::new($name);
    }

    public static function phone(string $name): TelephoneField
    {
        return TelephoneField::new($name);
    }

    public static function url(string $name): UrlField
    {
        return UrlField::new($name);
    }

    public static function date(string $name): DateField
    {
        return DateField::new($name);
    }

    public static function datetime(string $name): DateTimeField
    {
        return DateTimeField::new($name);
    }

    public static function time(string $name): TimeField
    {
        return TimeField::new($name);
    }

    public static function dateMultiple(string $name): DateMultipleField
    {
        return DateMultipleField::new($name);
    }

    public static function datetimeMultiple(string $name): DateTimeMultipleField
    {
        return DateTimeMultipleField::new($name);
    }

    public static function timezone(string $name): TimezoneField
    {
        return TimezoneField::new($name);
    }

    public static function password(string $name): PasswordField
    {
        return PasswordField::new($name);
    }

    public static function repeat(string $name): RepeatField
    {
        return RepeatField::new($name);
    }

    public static function integer(string $name): IntegerField
    {
        return IntegerField::new($name);
    }

    public static function float(string $name): FloatField
    {
        return FloatField::new($name);
    }

    public static function percent(string $name): PercentField
    {
        return PercentField::new($name);
    }

    public static function money(string $name): MoneyField
    {
        return MoneyField::new($name);
    }

    public static function color(string $name): ColorField
    {
        return ColorField::new($name);
    }

    public static function signature(string $name): SignatureField
    {
        return SignatureField::new($name);
    }

    public static function media(string $name): MediaField
    {
        return MediaField::new($name);
    }

    public static function file(string $name): FileField
    {
        return FileField::new($name);
    }

    public static function image(string $name): ImageField
    {
        return ImageField::new($name);
    }

    public static function array(string $name): ArrayField
    {
        return ArrayField::new($name);
    }

    public static function collection(string $name): CollectionField
    {
        return CollectionField::new($name);
    }

    public static function association(string $name): AssociationField
    {
        return AssociationField::new($name);
    }

    public static function user(string $name): UserField
    {
        return UserField::new($name);
    }

    public static function userAvatar(string $name): MediaField
    {
        return MediaField::new($name)
            ->setConf('public_user_images')
            ->setSizeDetail('md')
            ->setTemplatePath('field/userAvatar.html.twig');
    }

    public static function role(string $name): AssociationField
    {
        return AssociationField::new($name)
            ->setTemplatePath('field/role.html.twig');
    }
}
