<?php

namespace App\Field;

use Symfony\Component\Form\AbstractType;

/**
 * Factories for the fields used in CRUD `configureFields()` and `App\Form\FormGenerator`.
 *
 * Each method returns a field you keep configuring by chaining (label, columns, required, plugin options).
 * The IDE lists those options on the returned type. `$name` is the entity property, except on layout methods.
 */
class FieldGenerator extends AbstractType
{
    /**
     * Opens a tab. Fields declared after it belong to this tab until the next one.
     *
     * @param string $name Tab label (plain text or translation key)
     */
    public static function tab(string $name): FormField
    {
        return FormField::tab($name);
    }

    /**
     * Groups the following fields in a panel (fieldset). Chain `setIcon()`, `isCollapsible()`, `isCollapsed()`.
     *
     * @param string $name Panel label (plain text or translation key)
     */
    public static function panel(string $name): FormField
    {
        return FormField::panel($name);
    }

    /**
     * Starts a horizontal row. Pass a Bootstrap breakpoint (`sm`, `md`, `lg`, `xl`) to stack the fields below that width.
     *
     * @param string $breakpointName Empty string keeps the row horizontal at every width
     */
    public static function row(string $breakpointName = ''): FormField
    {
        return FormField::row($breakpointName);
    }

    /**
     * Column inside the current row. An int is a 12-grid span (`6` = half); a string is a Bootstrap class (`col`, `col-md-4`).
     *
     * @param int|string $cols Grid span or Bootstrap column class
     */
    public static function col(int|string $cols = 'col'): FormField
    {
        return FormField::col($cols);
    }

    /**
     * Generic field for a property that has no specialised factory. EasyAdmin guesses the form type from the property.
     *
     * @param string $name Entity property name
     */
    public static function field(string $name): Field
    {
        return Field::new($name);
    }

    /**
     * Primary key. Shown on index and detail; EasyAdmin does not render it as an editable input.
     *
     * @param string $name Entity property name (usually `id`)
     */
    public static function id(string $name): IdField
    {
        return IdField::new($name);
    }

    /**
     * Single-line plain text.
     *
     * @param string $name Entity property name
     */
    public static function text(string $name): TextField
    {
        return TextField::new($name);
    }

    /**
     * Single-line text forced through an IMask pattern (phone, IBAN, custom). Chain `setPattern()` or `setMaskType()`.
     *
     * @param string $name Entity property name
     */
    public static function mask(string $name): MaskField
    {
        return MaskField::new($name);
    }

    /**
     * Value submitted with the form and omitted from the layout.
     *
     * @param string $name Entity property name
     */
    public static function hidden(string $name): HiddenField
    {
        return HiddenField::new($name);
    }

    /**
     * URL slug kept in sync with one or more source fields. Chain `setTarget()` with the source property name(s).
     *
     * @param string $name Entity property name
     */
    public static function slug(string $name): SlugField
    {
        return SlugField::new($name);
    }

    /**
     * Multi-line plain text. The textarea grows with the content up to a max height (`setMaxHeight()`).
     *
     * @param string $name Entity property name
     */
    public static function textarea(string $name): TextareaField
    {
        return TextareaField::new($name);
    }

    /**
     * Rich HTML edited with TinyMCE. Chain `setPreset()`, `setToolbar()`, or `setPlugins()` to change the chrome.
     *
     * @param string $name Entity property name
     */
    public static function texteditor(string $name): TextEditorField
    {
        return TextEditorField::new($name);
    }

    /**
     * Source code edited with Ace. Chain `setLanguage()` (`CODEEDITOR_LANGUAGE_*`) and `setTheme()` (`CODEEDITOR_THEME_*`).
     *
     * @param string $name Entity property name
     */
    public static function codeeditor(string $name): CodeEditorField
    {
        return CodeEditorField::new($name);
    }

    /**
     * Fixed list of options (select). Chain `setChoices()` or `setTransChoices()`, `isMultiple()`, or `isExpanded()` for radios/checkboxes.
     *
     * @param string $name Entity property name
     */
    public static function choice(string $name): ChoiceField
    {
        return ChoiceField::new($name);
    }

    /**
     * PHP enum already typed on the property. Option labels come from each case's `translationKey()`.
     *
     * @param string $name Entity property name
     */
    public static function enum(string $name): EnumField
    {
        return EnumField::new($name);
    }

    /**
     * Boolean checkbox.
     *
     * @param string $name Entity property name
     */
    public static function checkbox(string $name): BooleanField
    {
        return BooleanField::new($name);
    }

    /**
     * Boolean toggle switch (same field as `checkbox()`, already rendered as a switch).
     *
     * @param string $name Entity property name
     */
    public static function switch(string $name): BooleanField
    {
        return BooleanField::new($name)->isSwitch();
    }

    /**
     * Email address, with the email form type and validation.
     *
     * @param string $name Entity property name
     */
    public static function email(string $name): EmailField
    {
        return EmailField::new($name);
    }

    /**
     * Telephone number (`tel` input). For a strict digit pattern, use `mask()`.
     *
     * @param string $name Entity property name
     */
    public static function phone(string $name): TelephoneField
    {
        return TelephoneField::new($name);
    }

    /**
     * URL, with the URL form type. Chain `setDefaultProtocol()` to prepend a scheme when the value has none.
     *
     * @param string $name Entity property name
     */
    public static function url(string $name): UrlField
    {
        return UrlField::new($name);
    }

    /**
     * Calendar date (Flatpickr). Chain `setFormat()`, `setMin()`, `setMax()`.
     *
     * @param string $name Entity property name
     */
    public static function date(string $name): DateField
    {
        return DateField::new($name);
    }

    /**
     * Date and time (Flatpickr). Chain `setFormat($dateFormat, $timeFormat)`, `setMin()`, `setMax()`.
     *
     * @param string $name Entity property name
     */
    public static function datetime(string $name): DateTimeField
    {
        return DateTimeField::new($name);
    }

    /**
     * Time of day (Flatpickr). Chain `setFormat()`, `setMin()`, `setMax()`.
     *
     * @param string $name Entity property name
     */
    public static function time(string $name): TimeField
    {
        return TimeField::new($name);
    }

    /**
     * Several dates in one property (Flatpickr multiple mode). Chain `isRange()` for a from–to range.
     *
     * @param string $name Entity property name
     */
    public static function dateMultiple(string $name): DateMultipleField
    {
        return DateMultipleField::new($name);
    }

    /**
     * Several date-times in one property (Flatpickr multiple mode). Chain `isRange()` for a from–to range.
     *
     * @param string $name Entity property name
     */
    public static function datetimeMultiple(string $name): DateTimeMultipleField
    {
        return DateTimeMultipleField::new($name);
    }

    /**
     * Timezone select (Symfony timezone type, grouped by region).
     *
     * @param string $name Entity property name
     */
    public static function timezone(string $name): TimezoneField
    {
        return TimezoneField::new($name);
    }

    /**
     * Password, with show and generate buttons. Chain `isRepeated()` to require the password twice.
     *
     * @param string $name Entity property name
     */
    public static function password(string $name): PasswordField
    {
        return PasswordField::new($name);
    }

    /**
     * Two inputs of the same type that must match. Chain `setType()` with the inner form type. Password confirmation is `password()->isRepeated()`.
     *
     * @param string $name Entity property name
     */
    public static function repeat(string $name): RepeatField
    {
        return RepeatField::new($name);
    }

    /**
     * Whole number. Chain `setMin()` / `setMax()`, or `isSlider()` for a noUiSlider.
     *
     * @param string $name Entity property name
     */
    public static function integer(string $name): IntegerField
    {
        return IntegerField::new($name);
    }

    /**
     * Decimal number (default 2 decimals, step `0.1`). Chain `setDecimals()`, `setStep()`, or `isSlider()`.
     *
     * @param string $name Entity property name
     */
    public static function float(string $name): FloatField
    {
        return FloatField::new($name);
    }

    /**
     * Percentage. By default `15` is shown as 15%; chain `storedAsFractional()` when the property stores `0.15`.
     *
     * @param string $name Entity property name
     */
    public static function percent(string $name): PercentField
    {
        return PercentField::new($name);
    }

    /**
     * Money amount. Default currency is EUR and the value is the major unit (`1.99`); chain `storedAsCents()` when the property stores `199`.
     *
     * @param string $name Entity property name
     */
    public static function money(string $name): MoneyField
    {
        return MoneyField::new($name);
    }

    /**
     * Color picker (Spectrum). The value is a CSS color string. Chain `showAlpha()` for transparency.
     *
     * @param string $name Entity property name
     */
    public static function color(string $name): ColorField
    {
        return ColorField::new($name);
    }

    /**
     * Hand-drawn signature (Signature Pad). The property stores the exported image (PNG data URL by default).
     *
     * @param string $name Entity property name
     */
    public static function signature(string $name): SignatureField
    {
        return SignatureField::new($name);
    }

    /**
     * Pick an existing file from the Artgris manager (UxMedia). Chain `setConf()` with the manager config key (default `public_all`).
     *
     * @param string $name Entity property name
     */
    public static function media(string $name): MediaField
    {
        return MediaField::new($name);
    }

    /**
     * Upload a new file of any type into `public/media`. Chain `setDir()` and `setAccept()`.
     *
     * @param string $name Entity property name
     */
    public static function file(string $name): FileField
    {
        return FileField::new($name);
    }

    /**
     * Upload a new image into `public/media`. Chain `setDir()` and `setAccept()` (default `image/*`).
     *
     * @param string $name Entity property name
     */
    public static function image(string $name): ImageField
    {
        return ImageField::new($name);
    }

    /**
     * Editable list of scalar values (add/remove rows). For a list of embedded objects, use `collection()`.
     *
     * @param string $name Entity property name
     */
    public static function array(string $name): ArrayField
    {
        return ArrayField::new($name);
    }

    /**
     * One-to-many or embeddable edited as repeated sub-forms. Chain `setEntryType()` / `useEntryCrudForm()` and `allowAdd()` / `allowDelete()`.
     *
     * @param string $name Entity property name
     */
    public static function collection(string $name): CollectionField
    {
        return CollectionField::new($name);
    }

    /**
     * Related entity as a Tom Select. Chain `setQueryBuilder()` to filter the options and `autocomplete()` when the list is large.
     *
     * @param string $name Entity property name
     */
    public static function association(string $name): AssociationField
    {
        return AssociationField::new($name);
    }

    /**
     * User association with avatar. By default only active, verified users are listed. Chain `onlyActive(false)` / `onlyVerified(false)` to widen it.
     *
     * @param string $name Entity property name
     */
    public static function user(string $name): UserField
    {
        return UserField::new($name);
    }

    /**
     * User image from the `public_user_images` manager, rendered with the avatar template.
     *
     * @param string $name Entity property name
     */
    public static function userAvatar(string $name): MediaField
    {
        return MediaField::new($name)
            ->setConf('public_user_images')
            ->setSizeDetail('md')
            ->setTemplatePath('field/userAvatar.html.twig');
    }

    /**
     * Role association rendered with the role template. Chain `setQueryBuilder()` to limit which roles can be assigned.
     *
     * @param string $name Entity property name
     */
    public static function role(string $name): AssociationField
    {
        return AssociationField::new($name)
            ->setTemplatePath('field/role.html.twig');
    }

    /**
     * Index/detail links to another CRUD for the related object (or each item, when the value is a list). The object must expose `id` and stringify to the link text.
     *
     * @param string $name               Entity property name
     * @param string $crudControllerFqcn CRUD controller of the related entity (`App\Controller\Admin\Cruds\…CrudController`)
     */
    public static function entity(string $name, string $crudControllerFqcn): Field
    {
        $parts = explode('\\', $crudControllerFqcn);
        $crud = lcfirst(str_replace('CrudController', '', end($parts)));

        return Field::new($name)
            ->setTemplatePath('field/entity.html.twig')
            ->setCustomOptions([
                'crudController' => $crudControllerFqcn,
                'crud' => $crud,
            ]);
    }
}
