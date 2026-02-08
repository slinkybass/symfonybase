<?php

namespace App\Controller\Admin\Cruds;

use App\Controller\Admin\AbstractCrudController;
use App\Entity\DemoEntity;
use App\Entity\Enum\UserGender;
use App\Field\FieldGenerator;
use App\Form\DemoFormType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DemoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DemoEntity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud);

        $crud->setEntityLabelInSingular('Demo');
        $crud->setEntityLabelInPlural('Demo');

        $entity = $this->entity();
        if ($entity) {
            $pageTitle = 'Demo: ' . $entity;
            $editTitle = $this->translator->trans('page_title.edit', ['%entity_label_singular%' => $pageTitle], 'EasyAdminBundle');
            $crud->setPageTitle(Crud::PAGE_DETAIL, $pageTitle);
            $crud->setPageTitle(Crud::PAGE_EDIT, $editTitle);
        }

        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        $entity = $this->entity();

        $basicFieldsTab = FieldGenerator::tab('Basic');

        $idPanel = FieldGenerator::panel('ID')->setIcon('input-search');
        $id = FieldGenerator::id('id')
            ->setLabel('Default')
            ->setDisabled()
            ->setColumns(3);

        $textPanel = FieldGenerator::panel('TEXT')->setIcon('input-search');
        $text = FieldGenerator::text('text')
            ->setLabel('Default')
            ->setColumns(3);
        $textCustom = FieldGenerator::text('text2')
            ->setLabel('Required, min and max lenght, placeholder...')
            ->setMinLength(3)
            ->setMaxLength(10)
            ->setPlaceholder('This is a placeholder')
            ->setColumns(3);
        $textHelp = FieldGenerator::text('textHelp')
            ->setLabel('Text field with help text')
            ->setHelp('Help text can contain any <b>HTML</b> content')
            ->setColumns(3);
        $textRow = FieldGenerator::row();
        $email = FieldGenerator::email('email')
            ->setLabel('Email')
            ->setColumns(3);
        $phone = FieldGenerator::phone('phone')
            ->setLabel('Phone')
            ->setColumns(3);
        $url = FieldGenerator::url('url')
            ->setLabel('URL')
            ->setColumns(3);
        $password = FieldGenerator::password('password')
            ->setLabel('Single Password')
            ->setColumns(3);

        $confirmPanel = FieldGenerator::panel('CONFIRM FIELDS')->setIcon('input-search');
        $confirmPassword = FieldGenerator::password('confirmPassword')
            ->setLabel('Password')
            ->isRepeated()
            ->setColumns(6);
        $confirmEmail = FieldGenerator::repeat('confirmEmail')
            ->setLabel('Email')
            ->setType(EmailType::class)
            ->setColumns(6);

        $numbersPanel = FieldGenerator::panel('NUMBERS')->setIcon('input-search');
        $integer = FieldGenerator::integer('integerNumber')
            ->setLabel('Integer')
            ->setColumns(3);
        $integerPositive = FieldGenerator::integer('integerPositive')
            ->setLabel('Integer positive')
            ->setMin(0)
            ->setColumns(3);
        $float = FieldGenerator::float('floatNumber')
            ->setLabel('Float')
            ->setColumns(3);
        $floatPositive = FieldGenerator::float('floatPositive')
            ->setLabel('Float positive')
            ->setMin(0)
            ->setColumns(3);
        $floatStep05 = FieldGenerator::float('floatStep05')
            ->setLabel('Float with step 0.5')
            ->setStep(0.5)
            ->setColumns(3);
        $percent = FieldGenerator::percent('percent')
            ->setLabel('Percent')
            ->setColumns(3);
        $money = FieldGenerator::money('money')
            ->setLabel('Money')
            ->setColumns(3);
        $moneyCustomCurrency = FieldGenerator::money('moneyCustomCurrency')
            ->setLabel('Money with custom currency')
            ->setCurrency('USD')
            ->setColumns(3);
        $slider = FieldGenerator::integer('slider')
            ->setLabel('Slider')
            ->pluginSlider()
            ->setMin(3)
            ->setMax(8)
            ->setColumns(6);
        $sliderFloat = FieldGenerator::float('sliderFloat')
            ->setLabel('Slider float with step 0.5 and pips and with min 0 and max 10')
            ->pluginSlider()
            ->setMin(0)
            ->setMax(10)
            ->setStep(0.5)
            ->sliderPips()
            ->setColumns(6);

        $checkboxPanel = FieldGenerator::panel('CHECKBOX')->setIcon('input-search');
        $checkbox = FieldGenerator::checkbox('checkbox')
            ->setLabel('Checkbox')
            ->setColumns(3);
        $switch = FieldGenerator::switch('switch')
            ->setLabel('Switch')
            ->setColumns(3);

        $colorPanel = FieldGenerator::panel('COLOR')->setIcon('input-search');
        $color = FieldGenerator::color('color')
            ->setLabel('Default')
            ->setColumns(4);
        $colorPalette = FieldGenerator::color('colorPalette')
            ->setLabel('Palette')
            ->showPalette()
            ->setColumns(4);
        $colorPaletteOnly = FieldGenerator::color('colorPaletteOnly')
            ->setLabel('Palette only')
            ->isPaletteOnly()
            ->setColumns(4);
        $colorInline = FieldGenerator::color('colorInline')
            ->setLabel('Inline')
            ->setType('flat')
            ->setColumns(4);
        $colorInlinePalette = FieldGenerator::color('colorInlinePalette')
            ->setLabel('Inline palette')
            ->setType('flat')
            ->showPalette()
            ->setColumns(4);
        $colorInlinePaletteOnly = FieldGenerator::color('colorInlinePaletteOnly')
            ->setLabel('Inline palette only')
            ->setType('flat')
            ->isPaletteOnly()
            ->setColumns(4);
        $colorAlpha = FieldGenerator::color('colorAlpha')
            ->setLabel('RGBA')
            ->showAlpha()
            ->setPreferredFormat('rgb')
            ->setColumns(4);

        $maskPanel = FieldGenerator::panel('MASK')->setIcon('input-search');
        $mask = FieldGenerator::mask('mask')
            ->setLabel('With a simple mask <small class="text-muted">+34 000 000 000</small>')
            ->setPattern('+34 000 000 000')
            ->setColumns(4);
        $mask2 = FieldGenerator::mask('mask2')
            ->setLabel('With a regex mask <small class="text-muted">^\d+$</small>')
            ->setPattern('^\d+$')
            ->isRegex()
            ->setColumns(4);
        $mask3 = FieldGenerator::mask('mask3')
            ->setLabel('Overwrite mode and placeholder <small class="text-muted">000000</small>')
            ->setPattern('000000')
            ->isOverwrite()
            ->setMaskPlaceholder('_')
            ->setColumns(4);

        $slugPanel = FieldGenerator::panel('SLUG')->setIcon('input-search');
        $slugTarget = FieldGenerator::text('slugTarget')
            ->setLabel('Target slug')
            ->setColumns(4);
        $slug = FieldGenerator::slug('slug')
            ->setLabel('Slug result')
            ->setTarget('slugTarget')
            ->setColumns(4);
        $slug2 = FieldGenerator::slug('slug2')
            ->setLabel('Slug result with confirmation message')
            ->setTarget('slugTarget')
            ->setConfirmText('If the slug is not valid or contains special characters or spaces, it will not work.<br /><strong>Are you sure you want to overwrite?</strong>')
            ->setColumns(4);

        $textareaFieldsTab = FieldGenerator::tab('Textarea');

        $textareaPanel = FieldGenerator::panel('TEXTAREA')->setIcon('input-search');
        $textarea = FieldGenerator::textarea('textarea')
            ->setLabel('Default with autogrow')
            ->setColumns(3);
        $textarea2 = FieldGenerator::textarea('textarea2')
            ->setLabel('Autogrow with custom rows and max height')
            ->setRows(2)
            ->setMaxHeight('80px')
            ->setColumns(3);
        $textarea3 = FieldGenerator::textarea('textarea3')
            ->setLabel('Disable autogrow')
            ->plugin(false)
            ->setColumns(3);
        $textarea4 = FieldGenerator::textarea('textarea4')
            ->setLabel('Disable autogrow and resize')
            ->plugin(false)
            ->isResizeable(false)
            ->setColumns(3);

        $texteditorPanel = FieldGenerator::panel('TEXTEDITOR')->setIcon('input-search');
        $texteditor = FieldGenerator::texteditor('texteditor')
            ->setLabel('Default')
            ->setColumns(4);
        $texteditor2 = FieldGenerator::texteditor('texteditor2')
            ->setLabel('Enable resize and disable spellcheck')
            ->isResizeable()
            ->isSpellcheck(false)
            ->setColumns(4);
        $texteditor3 = FieldGenerator::texteditor('texteditor3')
            ->setLabel('Custom toolbar')
            ->setToolbar('undo redo | bold italic underline | emoticons')
            ->setColumns(4);

        $codeeditorPanel = FieldGenerator::panel('CODEEDITOR')->setIcon('input-search');
        $codeeditor = FieldGenerator::codeeditor('codeeditor')
            ->setLabel('Default')
            ->setColumns(6);
        $codeeditor2 = FieldGenerator::codeeditor('codeeditor2')
            ->setLabel('With custom language (PHP) and theme (Twilight)')
            ->setTheme('twilight')
            ->setLanguage('php')
            ->setColumns(6);

        $choiceFieldsTab = FieldGenerator::tab('Choice');

        $choicePanel = FieldGenerator::panel('CHOICE')->setIcon('input-search');
        $choice = FieldGenerator::choice('choice')
            ->setLabel('Single choice')
            ->setColumns(4);
        $choice2 = FieldGenerator::choice('choice2')
            ->setLabel('Single choice expanded optional')
            ->isExpanded()
            ->setColumns(4);
        $choice3 = FieldGenerator::choice('choice3')
            ->setLabel('Single choice expanded required')
            ->isExpanded()
            ->isRequired()
            ->setColumns(4);
        $choiceRow = FieldGenerator::row();
        $choice4 = FieldGenerator::choice('choice4')
            ->setLabel('Multiple choice')
			->setChoices(UserGender::getChoices())
            ->isMultiple()
            ->setColumns(4);
        $choice5 = FieldGenerator::choice('choice5')
            ->setLabel('Multiple choice expanded')
			->setChoices(UserGender::getChoices())
            ->isExpanded()
            ->isMultiple()
            ->setColumns(4);

        $dateFieldsTab = FieldGenerator::tab('Date');

        $datePanel = FieldGenerator::panel('DATE')->setIcon('input-search');
        $date = FieldGenerator::date('date')
            ->setLabel('Date')
            ->setColumns(4);
        $datetime = FieldGenerator::datetime('datetime')
            ->setLabel('Datetime')
            ->setColumns(4);
        $time = FieldGenerator::time('time')
            ->setLabel('Time')
            ->setColumns(4);

        $dateDefaultValuesPanel = FieldGenerator::panel('DEFAULT VALUES')->setIcon('input-search');
        $dateDefaultValue = FieldGenerator::date('dateDefaultValue')
            ->setLabel('Default date value')
            ->setColumns(4);
        if ($entity && !$entity->getDateDefaultValue()) {
            $dateDefaultValue->setData(new \DateTime('today'));
        }
        $datetimeDefaultValue = FieldGenerator::datetime('datetimeDefaultValue')
            ->setLabel('Default datetime value')
            ->setColumns(4);
        if ($entity && !$entity->getDatetimeDefaultValue()) {
            $datetimeDefaultValue->setData(new \DateTime('1992-03-07 16:20'));
        }
        $timeDefaultValue = FieldGenerator::time('timeDefaultValue')
            ->setLabel('Default time value')
            ->setColumns(4);
        if ($entity && !$entity->getTimeDefaultValue()) {
            $timeDefaultValue->setData(new \DateTime('21:38'));
        }

        $dateMinMaxPanel = FieldGenerator::panel('MIN/MAX')->setIcon('input-search');
        $dateMinMax = FieldGenerator::date('dateMinMax')
            ->setLabel('Min and max date')
            ->setMin('2024-01-15')
            ->setMax('2024-01-21')
            ->setColumns(4);
        $datetimeMinMax = FieldGenerator::datetime('datetimeMinMax')
            ->setLabel('Min and max datetime')
            ->setMin('1992-03-07 06:00')
            ->setMax('1992-03-15 10:00')
            ->setColumns(4);
        $timeMinMax = FieldGenerator::time('timeMinMax')
            ->setLabel('Min and max time')
            ->setMin('06:00')
            ->setMax('10:00')
            ->setColumns(4);

        $dateEnableDisablePanel = FieldGenerator::panel('ENABLE/DISABLE DATES')->setIcon('input-search');
        $dateEnable = FieldGenerator::date('dateEnable')
            ->setLabel('Enabled dates')
            ->setEnabledDates(['1992-03-07', '1992-03-15'])
            ->setColumns(4);
        $dateDisable = FieldGenerator::date('dateDisable')
            ->setLabel('Disabled dates')
            ->setDisabledDates([new \DateTime('tomorrow'), new \DateTime('yesterday')])
            ->setColumns(4);

        // Multiple dates should be setted in a FormBuilder EventListener (preferably into another entity or an array property)
        $dateMultiplePanel = FieldGenerator::panel('MULTIPLE')->setIcon('input-search');
        $dateMultiple = FieldGenerator::dateMultiple('dateMultiple')
            ->setLabel('Multiple dates')
            ->setColumns(4);
        $datetimeMultiple = FieldGenerator::datetimeMultiple('datetimeMultiple')
            ->setLabel('Multiple datetimes')
            ->setColumns(4);
        $dateMultipleRow = FieldGenerator::row();
        $dateMultipleCustom = FieldGenerator::dateMultiple('dateMultipleCustom')
            ->setLabel('Multiple dates with enabled dates')
            ->setEnabledDates(['1992-03-07', '1992-03-09', '1992-03-12', '1992-03-15'])
            ->setColumns(4);
        $datetimeMultipleCustom = FieldGenerator::datetimeMultiple('datetimeMultipleCustom')
            ->setLabel('Multiple datetimes with min and max datetime')
            ->setMin('1992-03-07 06:00')
            ->setMax('1992-03-15 10:00')
            ->setColumns(4);

        // Range dates should be setted in a FormBuilder EventListener (preferably into dateStart and dateEnd properties)
        $dateRangePanel = FieldGenerator::panel('RANGE')->setIcon('input-search');
        $dateRange = FieldGenerator::dateMultiple('dateRange')
            ->setLabel('Range dates')
            ->isRange()
            ->setColumns(4);
        $datetimeRange = FieldGenerator::datetimeMultiple('datetimeRange')
            ->setLabel('Range datetimes')
            ->isRange()
            ->setColumns(4);
        $dateRangeCustom = FieldGenerator::dateMultiple('dateRangeCustom')
            ->setLabel('Range dates with min, max and disabled dates')
            ->isRange()
            ->setMin('1992-03-09')
            ->setMax('1992-03-15')
            ->setDisabledDates(['1992-03-12'])
            ->setColumns(4);

        $dateInlinePanel = FieldGenerator::panel('INLINE')->setIcon('input-search');
        $dateInline = FieldGenerator::date('dateInline')
            ->setLabel('Inline date')
            ->isInline()
            ->setColumns(4);
        $datetimeInline = FieldGenerator::datetime('datetimeInline')
            ->setLabel('Inline datetime')
            ->isInline()
            ->setColumns(4);
        $timeInline = FieldGenerator::time('timeInline')
            ->setLabel('Inline datetime')
            ->isInline()
            ->setColumns(4);

        $collectionFieldsTab = FieldGenerator::tab('Collection');

        $collectionPanel = FieldGenerator::panel('COLLECTION')->setIcon('input-search');
        $array = FieldGenerator::array('array')
            ->setLabel('Array')
            ->setColumns(4);
        $collection = FieldGenerator::collection('collection')
            ->setLabel('Collection')
            ->renderExpanded(true)
            ->setColumns(4);
        $collectionFormType = FieldGenerator::collection('collectionFormType')
            ->setLabel('Collection with a FormType')
            ->setEntryType(DemoFormType::class)
            ->setColumns(4);

        $mediaFieldsTab = FieldGenerator::tab('Media');

        $mediaPanel = FieldGenerator::panel('MEDIA')->setIcon('input-search');
        $media = FieldGenerator::media('media')
            ->setLabel('Default')
            ->setColumns(4);
        $mediaImage = FieldGenerator::media('mediaImage')
            ->setLabel('Image')
            ->setConf('public_images')
            ->setColumns(4);
        $mediaImageUsers = FieldGenerator::media('mediaImageUsers')
            ->setLabel('User image')
            ->setConf('public_user_images')
            ->setColumns(4);
        $mediaWithoutFileManager = FieldGenerator::media('mediaWithoutFileManager')
            ->setLabel('Force disable file manager')
            ->displayFileManager(false)
            ->setColumns(4);
        $mediaWithCrop = FieldGenerator::media('mediaWithCrop')
            ->setLabel('Enable crop')
            ->allowCrop()
            ->setColumns(4);

        $filesPanel = FieldGenerator::panel('FILES')->setIcon('input-search');
        $file = FieldGenerator::file('file')
            ->setLabel('File')
            ->setColumns(4);
        $filePDF = FieldGenerator::file('filePDF')
            ->setLabel('PDF')
            ->setAccept('application/pdf')
            ->setColumns(4);
        $image = FieldGenerator::image('image')
            ->setLabel('Image')
            ->setAccept('image/*')
            ->setColumns(4);

        $hierarchyFieldsTab = FieldGenerator::tab('Hierarchy');

        $hierarchyPanel = FieldGenerator::panel('HIERARCHY')->setIcon('input-search');
        $hierarchySwitch = FieldGenerator::switch('hierarchySwitch')
            ->setLabel('Only on checked')
            ->setHtmlAttribute('data-hf-parent', 'hierarchySwitch')
            ->setColumns(6);
        $hierarchySwitchChild = FieldGenerator::text('hierarchySwitchChild')
            ->setLabel('Child text input')
            ->setHtmlAttribute('data-hf-child', 'hierarchySwitch')
            ->setColumns(6);
        $hierarchyRow1 = FieldGenerator::row();
        $hierarchySwitchChecked = FieldGenerator::switch('hierarchySwitchChecked')
            ->setLabel('Only on checked but checked by default')
            ->isChecked()
            ->setHtmlAttribute('data-hf-parent', 'hierarchySwitchChecked')
            ->setColumns(6);
        $hierarchySwitchCheckedChild = FieldGenerator::date('hierarchySwitchCheckedChild')
            ->setLabel('Child date input')
            ->isRequired()
            ->setHtmlAttribute('data-hf-child', 'hierarchySwitchChecked')
            ->setColumns(6);
        $hierarchyRow2 = FieldGenerator::row();
        $hierarchySwitchShow = FieldGenerator::switch('hierarchySwitchShow')
            ->setLabel('Readonly mode')
            ->setHtmlAttribute('data-hf-parent', 'hierarchySwitchShow')
            ->setColumns(6);
        $hierarchySwitchShowChild = FieldGenerator::text('hierarchySwitchShowChild')
            ->setLabel('Child text input')
            ->setHtmlAttribute('data-hf-child', 'hierarchySwitchShow')
            ->setHtmlAttribute('data-hf-show', 'true')
            ->isRequired()
            ->setColumns(6);
        $hierarchyRow3 = FieldGenerator::row();
        $hierarchySwitchDefaultValue = FieldGenerator::switch('hierarchySwitchDefaultValue')
            ->setLabel('Default value on show')
            ->setHtmlAttribute('data-hf-parent', 'hierarchySwitchDefaultValue')
            ->setColumns(6);
        $hierarchySwitchDefaultValueChild = FieldGenerator::text('hierarchySwitchDefaultValueChild')
            ->setLabel('Child text input')
            ->setHtmlAttribute('data-hf-child', 'hierarchySwitchDefaultValue')
            ->setHtmlAttribute('data-hf-default-value', 'This is the default value')
            ->setColumns(6);
        $hierarchyRow4 = FieldGenerator::row();
        $hierarchySwitchInvertValue = FieldGenerator::switch('hierarchySwitchInvertValue')
            ->setLabel('Only on unchecked')
            ->isChecked()
            ->setHtmlAttribute('data-hf-parent', 'hierarchySwitchInvertValue')
            ->setColumns(6);
        $hierarchySwitchInvertValueChild = FieldGenerator::text('hierarchySwitchInvertValueChild')
            ->setLabel('Child text input')
            ->setHtmlAttribute('data-hf-child', 'hierarchySwitchInvertValue')
            ->setHtmlAttribute('data-hf-parent-value', 'false')
            ->setColumns(6);
        $hierarchyRow5 = FieldGenerator::row();
        $hierarchyText = FieldGenerator::text('hierarchyText')
            ->setLabel('Fill to show the child')
            ->setHtmlAttribute('data-hf-parent', 'hierarchyText')
            ->setColumns(6);
        $hierarchyTextChild = FieldGenerator::text('hierarchyTextChild')
            ->setLabel('Child text input')
            ->setHtmlAttribute('data-hf-child', 'hierarchyText')
            ->setColumns(6);
        $hierarchyRow6 = FieldGenerator::row();
        $hierarchyTextCustomValue = FieldGenerator::text('hierarchyTextCustomValue')
            ->setLabel('Fill with "ShOw Me"')
            ->setHtmlAttribute('data-hf-parent', 'hierarchyTextCustomValue')
            ->setColumns(6);
        $hierarchyTextCustomValueChild = FieldGenerator::text('hierarchyTextCustomValueChild')
            ->setLabel('Child text input')
            ->setHtmlAttribute('data-hf-child', 'hierarchyTextCustomValue')
            ->setHtmlAttribute('data-hf-parent-value', 'ShOw Me')
            ->setColumns(6);
        $hierarchyRow7 = FieldGenerator::row();
        $hierarchyKeepValue = FieldGenerator::switch('hierarchyKeepValue')
            ->setLabel('Keep value on child, but the value keeps if unchecked.')
            ->setHtmlAttribute('data-hf-parent', 'hierarchyKeepValue')
            ->setColumns(6);
        $hierarchyKeepValueChild = FieldGenerator::text('hierarchyKeepValueChild')
            ->setLabel('Child text input')
            ->setHtmlAttribute('data-hf-child', 'hierarchyKeepValue')
            ->setHtmlAttribute('data-hf-keep-value', 'true')
            ->setColumns(6);
        $hierarchyRow8 = FieldGenerator::row();
        $hierarchySaveValue = FieldGenerator::switch('hierarchySaveValue')
            ->setLabel('Keep value on child, but the value is removed if unchecked.')
            ->setHtmlAttribute('data-hf-parent', 'hierarchySaveValue')
            ->setColumns(6);
        $hierarchySaveValueChild = FieldGenerator::text('hierarchySaveValueChild')
            ->setLabel('Child text input')
            ->setHtmlAttribute('data-hf-child', 'hierarchySaveValue')
            ->setHtmlAttribute('data-hf-save-value', 'true')
            ->setColumns(6);

        $signatureFieldsTab = FieldGenerator::tab('Signature');

        $signaturePanel = FieldGenerator::panel('SIGNATURE')->setIcon('input-search');
        $signature = FieldGenerator::signature('signature')
            ->setLabel('Default')
            ->setColumns(6);
        $signatureWithInput = FieldGenerator::signature('signatureWithInput')
            ->setLabel('You can show the input with the base64 result')
            ->showInput()
            ->setColumns(6);
        $signatureWithoutUndo = FieldGenerator::signature('signatureWithoutUndo')
            ->setLabel('Disable the option to undo the last change')
            ->showUndo(false)
            ->setColumns(6);
        $signatureWithoutClear = FieldGenerator::signature('signatureWithoutClear')
            ->setLabel('Disable the option to clear the whole signature')
            ->showClear(false)
            ->setColumns(6);

        yield from $this->yieldFields([
            $basicFieldsTab,

            $idPanel,
            $id,

            $textPanel,
            $text,
            $textCustom,
            $textHelp,
            $textRow,
            $email,
            $phone,
            $url,
            $password,

            $confirmPanel,
            $confirmPassword,
            $confirmEmail,

            $numbersPanel,
            $integer,
            $integerPositive,
            $float,
            $floatPositive,
            $floatStep05,
            $percent,
            $money,
            $moneyCustomCurrency,
            $slider,
            $sliderFloat,

            $checkboxPanel,
            $checkbox,
            $switch,

            $colorPanel,
            $color,
            $colorPalette,
            $colorPaletteOnly,
            $colorInline,
            $colorInlinePalette,
            $colorInlinePaletteOnly,
            $colorAlpha,

            $maskPanel,
            $mask,
            $mask2,
            $mask3,

            $slugPanel,
            $slugTarget,
            $slug,
            $slug2,

            $textareaFieldsTab,

            $textareaPanel,
            $textarea,
            $textarea2,
            $textarea3,
            $textarea4,

            $texteditorPanel,
            $texteditor,
            $texteditor2,
            $texteditor3,

            $codeeditorPanel,
            $codeeditor,
            $codeeditor2,

            $choiceFieldsTab,

            $choicePanel,
            $choice,
            $choice2,
            $choice3,
            $choiceRow,
            $choice4,
            $choice5,

            $dateFieldsTab,

            $datePanel,
            $date,
            $datetime,
            $time,

            $dateDefaultValuesPanel,
            $dateDefaultValue,
            $datetimeDefaultValue,
            $timeDefaultValue,

            $dateMinMaxPanel,
            $dateMinMax,
            $datetimeMinMax,
            $timeMinMax,

            $dateEnableDisablePanel,
            $dateEnable,
            $dateDisable,

            $dateMultiplePanel,
            $dateMultiple,
            $datetimeMultiple,
            $dateMultipleRow,
            $dateMultipleCustom,
            $datetimeMultipleCustom,

            $dateRangePanel,
            $dateRange,
            $datetimeRange,
            $dateRangeCustom,

            $dateInlinePanel,
            $dateInline,
            $datetimeInline,
            $timeInline,

            $collectionFieldsTab,

            $collectionPanel,
            $array,
            $collection,
            $collectionFormType,

            $mediaFieldsTab,

            $mediaPanel,
            $media,
            $mediaImage,
            $mediaImageUsers,
            $mediaWithoutFileManager,
            $mediaWithCrop,

            $filesPanel,
            $file,
            $filePDF,
            $image,

            $hierarchyFieldsTab,

            $hierarchyPanel,
            $hierarchySwitch,
            $hierarchySwitchChild,
            $hierarchyRow1,
            $hierarchySwitchChecked,
            $hierarchySwitchCheckedChild,
            $hierarchyRow2,
            $hierarchySwitchShow,
            $hierarchySwitchShowChild,
            $hierarchyRow3,
            $hierarchySwitchDefaultValue,
            $hierarchySwitchDefaultValueChild,
            $hierarchyRow4,
            $hierarchySwitchInvertValue,
            $hierarchySwitchInvertValueChild,
            $hierarchyRow5,
            $hierarchyText,
            $hierarchyTextChild,
            $hierarchyRow6,
            $hierarchyTextCustomValue,
            $hierarchyTextCustomValueChild,
            $hierarchyRow7,
            $hierarchyKeepValue,
            $hierarchyKeepValueChild,
            $hierarchyRow8,
            $hierarchySaveValue,
            $hierarchySaveValueChild,

            $signatureFieldsTab,

            $signaturePanel,
            $signature,
            $signatureWithInput,
            $signatureWithoutUndo,
            $signatureWithoutClear,
        ]);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions;
    }
}
