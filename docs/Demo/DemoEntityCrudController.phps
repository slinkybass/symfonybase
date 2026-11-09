<?php

namespace App\Controller\Admin\Cruds;

use App\Controller\Admin\AbstractCrudController;
use App\Entity\DemoEntity;
use App\Entity\Enum\UserGender;
use App\Field\CodeEditorField;
use App\Field\ColorField;
use App\Field\DateField;
use App\Field\DateMultipleField;
use App\Field\FieldGenerator;
use App\Field\FloatField;
use App\Field\MaskField;
use App\Field\SignatureField;
use App\Field\TextEditorField;
use App\Form\Type\DemoEntityType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class DemoEntityCrudController extends AbstractCrudController
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
            $pageTitle = 'Demo: '.$entity;
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
            ->setLabel('Default <small class="text-muted">disabled</small>')
            ->isDisabled()
            ->setColumns(3);

        $textPanel = FieldGenerator::panel('TEXT')->setIcon('input-search');
        $text = FieldGenerator::text('text')
            ->setLabel('Default')
            ->setColumns(3);
        $textRequired = FieldGenerator::text('textRequired')
            ->setLabel('Required <small class="text-muted">min 3 / max 10</small>')
            ->setMinLength(3)
            ->setMaxLength(10)
            ->setPlaceholder('This is a placeholder')
            ->setColumns(3);
        $textHelp = FieldGenerator::text('textHelp')
            ->setLabel('With help')
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
            ->setLabel('Password')
            ->setColumns(3);

        $confirmPanel = FieldGenerator::panel('CONFIRM FIELDS')->setIcon('input-search');
        $confirmPassword = FieldGenerator::password('confirmPassword')
            ->setLabel('Repeated password')
            ->isRepeated()
            ->setColumns(6);
        $confirmEmail = FieldGenerator::repeat('confirmEmail')
            ->setLabel('Repeated email')
            ->setType(EmailType::class)
            ->setColumns(6);

        $numbersPanel = FieldGenerator::panel('NUMBERS')->setIcon('input-search');
        $integerNumber = FieldGenerator::integer('integerNumber')
            ->setLabel('Integer')
            ->setColumns(3);
        $integerPositive = FieldGenerator::integer('integerPositive')
            ->setLabel('Positive <small class="text-muted">setMin(0)</small>')
            ->setMin(0)
            ->setColumns(3);
        $floatNumber = FieldGenerator::float('floatNumber')
            ->setLabel('Float')
            ->setColumns(3);
        $floatPositive = FieldGenerator::float('floatPositive')
            ->setLabel('Positive <small class="text-muted">setMin(0)</small>')
            ->setMin(0)
            ->setColumns(3);
        $floatStepHalf = FieldGenerator::float('floatStepHalf')
            ->setLabel('Step 0.5 <small class="text-muted">setStep</small>')
            ->setStep(0.5)
            ->setColumns(3);
        $percent = FieldGenerator::percent('percent')
            ->setLabel('Percent')
            ->setColumns(3);
        $money = FieldGenerator::money('money')
            ->setLabel('Money')
            ->setColumns(3);
        $moneyCustomCurrency = FieldGenerator::money('moneyCustomCurrency')
            ->setLabel('Custom currency <small class="text-muted">USD</small>')
            ->setCurrency('USD')
            ->setColumns(3);

        $checkboxPanel = FieldGenerator::panel('CHECKBOX')->setIcon('input-search');
        $checkbox = FieldGenerator::checkbox('checkbox')
            ->setLabel('Checkbox')
            ->setColumns(3);
        $switch = FieldGenerator::switch('switch')
            ->setLabel('Switch')
            ->setColumns(3);

        $filesPanel = FieldGenerator::panel('FILES')->setIcon('input-search');
        $file = FieldGenerator::file('file')
            ->setLabel('File')
            ->setColumns(4);
        $filePDF = FieldGenerator::file('filePDF')
            ->setLabel('PDF <small class="text-muted">setAccept</small>')
            ->setAccept('application/pdf')
            ->setColumns(4);
        $image = FieldGenerator::image('image')
            ->setLabel('Image <small class="text-muted">image/*</small>')
            ->setAccept('image/*')
            ->setColumns(4);

        $textareaPanel = FieldGenerator::panel('TEXTAREA')->setIcon('input-search');
        $textarea = FieldGenerator::textarea('textarea')
            ->setLabel('Default <small class="text-muted">autogrow</small>')
            ->setColumns(3);
        $textareaBounds = FieldGenerator::textarea('textareaBounds')
            ->setLabel('Bounds <small class="text-muted">rows 2 / max 80px</small>')
            ->setRows(2)
            ->setMaxHeight('80px')
            ->setColumns(3);
        $textareaNoAutogrow = FieldGenerator::textarea('textareaNoAutogrow')
            ->setLabel('No autogrow <small class="text-muted">enablePlugin(false)</small>')
            ->enablePlugin(false)
            ->setColumns(3);
        $textareaStatic = FieldGenerator::textarea('textareaStatic')
            ->setLabel('Static <small class="text-muted">no autogrow / no resize</small>')
            ->enablePlugin(false)
            ->isResizeable(false)
            ->setColumns(3);

        $choicePanel = FieldGenerator::panel('CHOICE')->setIcon('input-search');
        $choice = FieldGenerator::enum('choice')
            ->setLabel('Single <small class="text-muted">native</small>')
            ->enablePlugin(false)
            ->setColumns(4);
        $choiceExpanded = FieldGenerator::enum('choiceExpanded')
            ->setLabel('Expanded')
            ->isExpanded()
            ->setColumns(4);
        $choiceExpandedRequired = FieldGenerator::enum('choiceExpandedRequired')
            ->setLabel('Expanded required')
            ->isExpanded()
            ->isRequired()
            ->setColumns(4);
        $choiceRow = FieldGenerator::row();
        $choiceMultiple = FieldGenerator::choice('choiceMultiple')
            ->setLabel('Multiple <small class="text-muted">native</small>')
            ->setChoices(UserGender::choices())
            ->isMultiple()
            ->enablePlugin(false)
            ->setColumns(4);
        $choiceMultipleExpanded = FieldGenerator::choice('choiceMultipleExpanded')
            ->setLabel('Multiple expanded')
            ->setChoices(UserGender::choices())
            ->isExpanded()
            ->isMultiple()
            ->setColumns(4);

        $codeeditorFieldsTab = FieldGenerator::tab('Code editor');

        $codeeditorPanel = FieldGenerator::panel('BASIC')->setIcon('input-search');
        $codeeditor = FieldGenerator::codeeditor('codeeditor')
            ->setLabel('Default')
            ->setColumns(4);
        $codeeditorPhpGithub = FieldGenerator::codeeditor('codeeditorPhpGithub')
            ->setLabel('PHP + GitHub <small class="text-muted">light/dark themes</small>')
            ->setTheme(CodeEditorField::CODEEDITOR_THEME_GITHUB)
            ->setDarkTheme(CodeEditorField::CODEEDITOR_THEME_GITHUB_DARK)
            ->setLanguage(CodeEditorField::CODEEDITOR_LANGUAGE_PHP)
            ->setColumns(4);
        $codeeditorPlaceholder = FieldGenerator::codeeditor('codeeditorPlaceholder')
            ->setLabel('Placeholder')
            ->setPlaceholder('// type some code…')
            ->setColumns(4);

        $codeeditorSizePanel = FieldGenerator::panel('SIZE')->setIcon('input-search');
        $codeeditorBounds = FieldGenerator::codeeditor('codeeditorBounds')
            ->setLabel('Bounds + font <small class="text-muted">10–50 lines / 16px</small>')
            ->setMinLines(10)
            ->setMaxLines(50)
            ->setFontSize(16)
            ->setFontFamily('"Courier New", Courier, monospace')
            ->setColumns(6);
        $codeeditorIndentSpaces = FieldGenerator::codeeditor('codeeditorIndentSpaces')
            ->setLabel('Spaces indent <small class="text-muted">tabSize 2</small>')
            ->isIndentedWithTabs(false)
            ->setTabSize(2)
            ->setColumns(6);

        $codeeditorDisplayPanel = FieldGenerator::panel('DISPLAY')->setIcon('input-search');
        $codeeditorWrap = FieldGenerator::codeeditor('codeeditorWrap')
            ->setLabel('Wrap <small class="text-muted">no line numbers</small>')
            ->isWrap()
            ->showLineNumbers(false)
            ->setColumns(4);
        $codeeditorPrintMargin = FieldGenerator::codeeditor('codeeditorPrintMargin')
            ->setLabel('Print margin <small class="text-muted">50 + invisibles</small>')
            ->showPrintMargin()
            ->setPrintMarginColumn(50)
            ->showInvisibles()
            ->setColumns(4);
        $codeeditorMinimalUi = FieldGenerator::codeeditor('codeeditorMinimalUi')
            ->setLabel('Minimal UI <small class="text-muted">no gutter / worker / guides</small>')
            ->isHighlightSelectedWord(false)
            ->isUseWorker(false)
            ->showGutter(false)
            ->isHighlightActiveLine(false)
            ->showIndentGuides(false)
            ->setColumns(4);

        $codeeditorStatePanel = FieldGenerator::panel('STATE')->setIcon('input-search');
        $codeeditorReadonly = FieldGenerator::codeeditor('codeeditorReadonly')
            ->setLabel('Readonly')
            ->isReadonly()
            ->setColumns(12);

        $collectionFieldsTab = FieldGenerator::tab('Collection');

        $collectionPanel = FieldGenerator::panel('BASIC')->setIcon('input-search');
        $array = FieldGenerator::array('array')
            ->setLabel('Array <small class="text-muted">simple strings</small>')
            ->setColumns(6);
        $collection = FieldGenerator::collection('collection')
            ->setLabel('Default <small class="text-muted">expanded</small>')
            ->isExpanded()
            ->setColumns(6);

        $collectionOptionsPanel = FieldGenerator::panel('OPTIONS')->setIcon('input-search');
        $collectionNoAdd = FieldGenerator::collection('collectionNoAdd')
            ->setLabel('No add <small class="text-muted">allowAdd(false)</small>')
            ->allowAdd(false)
            ->setColumns(4);
        if ($entity && !$entity->getCollectionNoAdd()) {
            $collectionNoAdd->setData(['Alpha', 'Beta']);
        }
        $collectionNoDelete = FieldGenerator::collection('collectionNoDelete')
            ->setLabel('No delete <small class="text-muted">allowDelete(false)</small>')
            ->allowDelete(false)
            ->setColumns(4);
        if ($entity && !$entity->getCollectionNoDelete()) {
            $collectionNoDelete->setData(['One', 'Two']);
        }
        $collectionLabels = FieldGenerator::collection('collectionLabels')
            ->setLabel('Entry labels <small class="text-muted">showEntryLabel</small>')
            ->showEntryLabel()
            ->setColumns(4);

        $collectionPluginsPanel = FieldGenerator::panel('HIERARCHY + PLUGINS')->setIcon('input-search');
        $collectionPlugins = FieldGenerator::collection('collectionPlugins')
            ->setLabel('Hierarchy + plugins <small class="text-muted">data-hf-* / date / color / mask</small>')
            ->setEntryType(DemoEntityType::class)
            ->isEntryComplex()
            ->isExpanded()
            ->isRequired()
            ->setEntryToStringMethod(static fn ($value) => \is_array($value) ? (string) ($value['title'] ?? '') : (string) $value)
            ->setColumns(12);
        if ($entity && !$entity->getCollectionPlugins()) {
            $collectionPlugins->setData([
                [
                    'enabled' => true,
                    'disabledNote' => '',
                    'title' => 'Sample',
                    'mode' => 'custom',
                    'simpleNote' => '',
                    'date' => new \DateTimeImmutable('tomorrow'),
                    'color' => '#206bc4',
                    'choice' => 'b',
                    'phone' => '',
                    'choiceDetail' => 'Because choice is B',
                ],
            ]);
        }

        $selectFieldsTab = FieldGenerator::tab('Select');

        $selectChoices = [
            'Apple' => 'apple',
            'Banana' => 'banana',
            'Cherry' => 'cherry',
            'Date' => 'date',
            'Elderberry' => 'elderberry',
            'Fig' => 'fig',
            'Grape' => 'grape',
            'Honeydew' => 'honeydew',
            'Kiwi' => 'kiwi',
            'Lemon' => 'lemon',
            'Mango' => 'mango',
            'Nectarine' => 'nectarine',
        ];
        $selectDiacriticChoices = [
            'Café' => 'cafe',
            'Niño' => 'nino',
            'Año' => 'ano',
            'México' => 'mexico',
            'España' => 'espana',
            'Pingüino' => 'pinguino',
        ];

        $selectBasicPanel = FieldGenerator::panel('BASIC')->setIcon('input-search');
        $selectDefault = FieldGenerator::choice('selectDefault')
            ->setLabel('Default')
            ->setChoices($selectChoices)
            ->setColumns(4);
        $selectEnum = FieldGenerator::enum('selectEnum')
            ->setLabel('Enum <small class="text-muted">UserGender</small>')
            ->setColumns(4);
        $selectRequired = FieldGenerator::choice('selectRequired')
            ->setLabel('Required <small class="text-muted">no clear by default</small>')
            ->setChoices($selectChoices)
            ->isRequired()
            ->setColumns(4);

        $selectCreatePanel = FieldGenerator::panel('CREATE')->setIcon('input-search');
        $selectCreate = FieldGenerator::choice('selectCreate')
            ->setLabel('Allow create <small class="text-muted">setAllowItemCreate</small>')
            ->setChoices($selectChoices)
            ->setAllowItemCreate()
            ->setColumns(4);
        $selectCreateOnBlur = FieldGenerator::choice('selectCreateOnBlur')
            ->setLabel('Create on blur <small class="text-muted">isCreateOnBlur</small>')
            ->setChoices($selectChoices)
            ->setAllowItemCreate()
            ->isCreateOnBlur()
            ->setColumns(4);
        $selectHtml = FieldGenerator::choice('selectHtml')
            ->setLabel('HTML labels <small class="text-muted">renderAsHtml</small>')
            ->setChoices([
                '<b>Bold</b> option' => 'bold',
                '<em>Italic</em> option' => 'italic',
                '<span class="badge badge-primary text-primary-fg">Badge</span>' => 'badge',
            ])
            ->renderAsHtml()
            ->setColumns(4);

        $selectLimitsPanel = FieldGenerator::panel('LIMITS')->setIcon('input-search');
        $selectMultiple = FieldGenerator::choice('selectMultiple')
            ->setLabel('Multiple <small class="text-muted">isMultiple</small>')
            ->setChoices($selectChoices)
            ->isMultiple()
            ->setColumns(4);
        $selectMaxOptions = FieldGenerator::choice('selectMaxOptions')
            ->setLabel('Max options <small class="text-muted">setMaxOptions(3)</small>')
            ->setChoices($selectChoices)
            ->setMaxOptions(3)
            ->setColumns(4);
        $selectMaxItems = FieldGenerator::choice('selectMaxItems')
            ->setLabel('Max items <small class="text-muted">setMaxItems(2)</small>')
            ->setChoices($selectChoices)
            ->isMultiple()
            ->setMaxItems(2)
            ->setColumns(4);

        $selectBehaviorPanel = FieldGenerator::panel('BEHAVIOR')->setIcon('input-search');
        $selectOnTab = FieldGenerator::choice('selectOnTab')
            ->setLabel('Select on Tab <small class="text-muted">isSelectOnTab</small>')
            ->setChoices($selectDiacriticChoices)
            ->isSelectOnTab()
            ->setColumns(3);
        $selectDiacritics = FieldGenerator::choice('selectDiacritics')
            ->setLabel('Diacritics off <small class="text-muted">isDiacritics(false)</small>')
            ->setHelp('Type <code>cafe</code> to match <em>Café</em> when diacritics are disabled.')
            ->setChoices($selectDiacriticChoices)
            ->isDiacritics(false)
            ->setColumns(3);
        $selectNoClear = FieldGenerator::choice('selectNoClear')
            ->setLabel('No clear <small class="text-muted">showClearButton(false)</small>')
            ->setChoices($selectDiacriticChoices)
            ->showClearButton(false)
            ->setColumns(3);
        $selectNoDropdown = FieldGenerator::choice('selectNoDropdown')
            ->setLabel('No dropdown input <small class="text-muted">showDropdownInput(false)</small>')
            ->setChoices($selectDiacriticChoices)
            ->showDropdownInput(false)
            ->setColumns(3);

        $selectMultipleExtrasPanel = FieldGenerator::panel('MULTIPLE EXTRAS')->setIcon('input-search');
        $selectCloseAfterSelect = FieldGenerator::choice('selectCloseAfterSelect')
            ->setLabel('Close after select <small class="text-muted">isCloseAfterSelect</small>')
            ->setChoices($selectChoices)
            ->isMultiple()
            ->isCloseAfterSelect()
            ->setColumns(4);
        $selectHideSelected = FieldGenerator::choice('selectHideSelected')
            ->setLabel('Show selected <small class="text-muted">isHideSelected(false)</small>')
            ->setChoices($selectChoices)
            ->isMultiple()
            ->isHideSelected(false)
            ->setColumns(4);
        $selectCheckbox = FieldGenerator::choice('selectCheckbox')
            ->setLabel('Checkbox options <small class="text-muted">showCheckboxOptions</small>')
            ->setChoices($selectChoices)
            ->isMultiple()
            ->showCheckboxOptions()
            ->setColumns(4);
        $selectNoRemove = FieldGenerator::choice('selectNoRemove')
            ->setLabel('No remove <small class="text-muted">showRemoveButton(false)</small>')
            ->setChoices($selectChoices)
            ->isMultiple()
            ->showRemoveButton(false)
            ->setColumns(6);
        $selectDuplicates = FieldGenerator::choice('selectDuplicates')
            ->setLabel('Duplicates <small class="text-muted">delimiter ;</small>')
            ->setChoices($selectChoices)
            ->isMultiple()
            ->isHideSelected(false)
            ->isDuplicates()
            ->setDelimiter(';')
            ->setColumns(6);

        $selectAssociationPanel = FieldGenerator::panel('ASSOCIATION')->setIcon('input-search');
        $selectRole = FieldGenerator::association('selectRole')
            ->setLabel('Role <small class="text-muted">no preload</small>')
            ->setHelp('Type to search. Dropdown opens when results arrive.')
            ->setCrudController(RoleCrudController::class)
            ->autocomplete()
            ->setPreload(false)
            ->showDropdownInput(false)
            ->isOpenOnFocus(false)
            ->setColumns(6);
        $selectUser = FieldGenerator::user('selectUser')
            ->setLabel('User <small class="text-muted">preload on focus</small>')
            ->setCrudController(UserCrudController::class)
            ->autocomplete()
            ->showDropdownInput(false)
            ->setColumns(6);

        $colorFieldsTab = FieldGenerator::tab('Color');

        $colorPanel = FieldGenerator::panel('COLOR')->setIcon('input-search');
        $color = FieldGenerator::color('color')
            ->setLabel('Default')
            ->setColumns(3);
        $colorFlat = FieldGenerator::color('colorFlat')
            ->setLabel('Flat <small class="text-muted">COLOR_TYPE_FLAT</small>')
            ->setType(ColorField::COLOR_TYPE_FLAT)
            ->setColumns(3);
        $colorAlpha = FieldGenerator::color('colorAlpha')
            ->setLabel('Alpha <small class="text-muted">RGBA</small>')
            ->showAlpha()
            ->setPreferredFormat(ColorField::COLOR_FORMAT_RGB)
            ->setColumns(3);
        $colorButtons = FieldGenerator::color('colorButtons')
            ->setLabel('Buttons <small class="text-muted">initial + allow empty</small>')
            ->showButtons()
            ->showInitial()
            ->isAllowEmpty()
            ->showInput()
            ->setPreferredFormat(ColorField::COLOR_FORMAT_HEX)
            ->setColumns(3);

        $colorPalettePanel = FieldGenerator::panel('PALETTE')->setIcon('input-search');
        $colorPalette = FieldGenerator::color('colorPalette')
            ->setLabel('Palette <small class="text-muted">showPalette</small>')
            ->showPalette()
            ->setColumns(6);
        $colorPaletteOnly = FieldGenerator::color('colorPaletteOnly')
            ->setLabel('Palette only <small class="text-muted">isPaletteOnly</small>')
            ->isPaletteOnly()
            ->setColumns(6);
        $colorFlatPalette = FieldGenerator::color('colorFlatPalette')
            ->setLabel('Flat + palette')
            ->setType(ColorField::COLOR_TYPE_FLAT)
            ->showPalette()
            ->setColumns(6);
        $colorFlatPaletteOnly = FieldGenerator::color('colorFlatPaletteOnly')
            ->setLabel('Flat + palette only')
            ->setType(ColorField::COLOR_TYPE_FLAT)
            ->isPaletteOnly()
            ->setColumns(6);
        $colorCustomPalette = FieldGenerator::color('colorCustomPalette')
            ->setLabel('Custom palette <small class="text-muted">hideAfterPaletteSelect</small>')
            ->isPaletteOnly()
            ->hideAfterPaletteSelect()
            ->setPalette([
                ['#000000', '#ffffff', '#f44336', '#4caf50'],
                ['#2196f3', '#ff9800', '#9c27b0', '#607d8b'],
            ])
            ->setColumns(4);
        $colorTogglePalette = FieldGenerator::color('colorTogglePalette')
            ->setLabel('Toggle palette <small class="text-muted">more / less</small>')
            ->isPaletteOnly()
            ->isTogglePaletteOnly()
            ->setTogglePaletteMoreText('more')
            ->setTogglePaletteLessText('less')
            ->setPalette([
                ['#000', '#444', '#666', '#999', '#ccc', '#eee', '#f3f3f3', '#fff'],
                ['#f00', '#f90', '#ff0', '#0f0', '#0ff', '#00f', '#90f', '#f0f'],
            ])
            ->setColumns(4);
        $colorSelection = FieldGenerator::color('colorSelection')
            ->setLabel('Selection palette <small class="text-muted">localStorage</small>')
            ->showPalette()
            ->showSelectionPalette()
            ->setLocalStorageKey('demo.spectrum.selection')
            ->setSelectionPalette(['#276cb8', '#e69138', '#6aa84f'])
            ->setMaxSelectionSize(6)
            ->setColumns(4);

        $dateFieldsTab = FieldGenerator::tab('Date & Time');

        $datePanel = FieldGenerator::panel('DATE')->setIcon('input-search');
        $date = FieldGenerator::date('date')
            ->setLabel('Date')
            ->setFormat(DateField::DISPLAY_FORMAT_MEDIUM)
            ->setColumns(4);
        $datetime = FieldGenerator::datetime('datetime')
            ->setLabel('Datetime')
            ->setFormat(DateField::DISPLAY_FORMAT_MEDIUM, DateField::DISPLAY_FORMAT_SHORT)
            ->setColumns(4);
        $time = FieldGenerator::time('time')
            ->setLabel('Time')
            ->setFormat(DateField::DISPLAY_FORMAT_SHORT)
            ->setColumns(4);

        $dateDefaultValuesPanel = FieldGenerator::panel('DEFAULT VALUES')->setIcon('input-search');
        $dateDefaultValue = FieldGenerator::date('dateDefaultValue')
            ->setLabel('Default value')
            ->setColumns(4);
        if ($entity && !$entity->getDateDefaultValue()) {
            $dateDefaultValue->setData(new \DateTime('tomorrow'));
        }
        $datetimeDefaultValue = FieldGenerator::datetime('datetimeDefaultValue')
            ->setLabel('Default value')
            ->setColumns(4);
        if ($entity && !$entity->getDatetimeDefaultValue()) {
            $datetimeDefaultValue->setData(new \DateTime('1992-03-07 16:20'));
        }
        $timeDefaultValue = FieldGenerator::time('timeDefaultValue')
            ->setLabel('Default value')
            ->setColumns(4);
        if ($entity && !$entity->getTimeDefaultValue()) {
            $timeDefaultValue->setData(new \DateTime('21:38'));
        }

        $dateMinMaxPanel = FieldGenerator::panel('MIN/MAX')->setIcon('input-search');
        $dateMinMax = FieldGenerator::date('dateMinMax')
            ->setLabel('Min / max')
            ->setMin('2024-01-15')
            ->setMax('2024-01-21')
            ->setColumns(4);
        $datetimeMinMax = FieldGenerator::datetime('datetimeMinMax')
            ->setLabel('Min / max')
            ->setMin('1992-03-07 06:00')
            ->setMax('1992-03-15 10:00')
            ->setColumns(4);
        $timeMinMax = FieldGenerator::time('timeMinMax')
            ->setLabel('Min / max')
            ->setMin('06:00')
            ->setMax('10:00')
            ->setColumns(4);

        $dateEnableDisablePanel = FieldGenerator::panel('ENABLE/DISABLE DATES')->setIcon('input-search');
        $dateEnable = FieldGenerator::date('dateEnable')
            ->setLabel('Enabled dates <small class="text-muted">setEnabledDates</small>')
            ->setEnabledDates(['1992-03-07', '1992-03-15'])
            ->setColumns(6);
        $dateDisable = FieldGenerator::date('dateDisable')
            ->setLabel('Disabled dates <small class="text-muted">setDisabledDates</small>')
            ->setDisabledDates([new \DateTime('tomorrow'), new \DateTime('yesterday')])
            ->setColumns(6);

        $dateMultiplePanel = FieldGenerator::panel('MULTIPLE')->setIcon('input-search');
        $dateMultiple = FieldGenerator::dateMultiple('dateMultiple')
            ->setLabel('Multiple <small class="text-muted">conjunction " | "</small>')
            ->setConjunction(' | ')
            ->setColumns(6);
        $datetimeMultiple = FieldGenerator::datetimeMultiple('datetimeMultiple')
            ->setLabel('Multiple')
            ->setColumns(6);
        $dateMultipleRow = FieldGenerator::row();
        $dateMultipleCustom = FieldGenerator::dateMultiple('dateMultipleCustom')
            ->setLabel('Multiple + enabled dates')
            ->setEnabledDates(['1992-03-07', '1992-03-09', '1992-03-12', '1992-03-15'])
            ->setColumns(6);
        $datetimeMultipleCustom = FieldGenerator::datetimeMultiple('datetimeMultipleCustom')
            ->setLabel('Multiple + min / max')
            ->setMin('1992-03-07 06:00')
            ->setMax('1992-03-15 10:00')
            ->setColumns(6);

        $dateRangePanel = FieldGenerator::panel('RANGE')->setIcon('input-search');
        $dateRange = FieldGenerator::dateMultiple('dateRange')
            ->setLabel('Range')
            ->isRange()
            ->setRangeSeparator(DateMultipleField::DATE_RANGE_SEPARATOR_DEFAULT)
            ->setColumns(4);
        $datetimeRange = FieldGenerator::datetimeMultiple('datetimeRange')
            ->setLabel('Range')
            ->isRange()
            ->setColumns(4);
        $dateRangeCustom = FieldGenerator::dateMultiple('dateRangeCustom')
            ->setLabel('Range + min / max / disabled')
            ->isRange()
            ->setMin('1992-03-09')
            ->setMax('1992-03-15')
            ->setDisabledDates(['1992-03-12'])
            ->setColumns(4);

        $dateInlinePanel = FieldGenerator::panel('INLINE')->setIcon('input-search');
        $dateInline = FieldGenerator::date('dateInline')
            ->setLabel('Inline')
            ->isInline()
            ->setColumns(4);
        $datetimeInline = FieldGenerator::datetime('datetimeInline')
            ->setLabel('Inline')
            ->isInline()
            ->setColumns(4);
        $timeInline = FieldGenerator::time('timeInline')
            ->setLabel('Inline')
            ->isInline()
            ->setColumns(4);

        $dateCalendarUiPanel = FieldGenerator::panel('CALENDAR UI')->setIcon('input-search');
        $dateCalendarUi = FieldGenerator::date('dateCalendarUi')
            ->setLabel('Two months <small class="text-muted">custom arrows</small>')
            ->setShowMonths(2)
            ->setPrevArrow('Prev')
            ->setNextArrow('Next')
            ->setColumns(6);
        $dateStatic = FieldGenerator::date('dateStatic')
            ->setLabel('Week numbers <small class="text-muted">shorthand month</small>')
            ->isShorthandCurrentMonth()
            ->isWeekNumbers()
            ->setColumns(6);

        $datePickerPanel = FieldGenerator::panel('PICKER')->setIcon('input-search');
        $datePickerOpts = FieldGenerator::date('datePickerOpts')
            ->setLabel('Above <small class="text-muted">no animation / stay open</small>')
            ->setPosition(DateField::DATE_POSITION_ABOVE)
            ->isAnimate(false)
            ->isCloseOnSelect(false)
            ->setColumns(4);
        $dateAltFormat = FieldGenerator::date('dateAltFormat')
            ->setLabel('Alt input <small class="text-muted">Moment LL</small>')
            ->isAltInput()
            ->setDateFormat('YYYY-MM-DD')
            ->setDateAltFormat('LL')
            ->setColumns(4);
        $dateNative = FieldGenerator::date('dateNative')
            ->setLabel('Native selects <small class="text-muted">renderAsChoice</small>')
            ->renderAsChoice()
            ->setColumns(4);

        $dateClockPanel = FieldGenerator::panel('CLOCK')->setIcon('input-search');
        $datetimeClock = FieldGenerator::datetime('datetimeClock')
            ->setLabel('Clock options <small class="text-muted">seconds / 15 min / 08:30–16:30</small>')
            ->isEnableSeconds()
            ->setMinuteIncrement(15)
            ->setDefaultHour(8)
            ->setDefaultMinute(30)
            ->isAutoFillDefaultTime()
            ->setMinTime('08:30')
            ->setMaxTime('16:30')
            ->setColumns(4);
        $timeHour12 = FieldGenerator::time('timeHour12')
            ->setLabel('12-hour <small class="text-muted">with seconds</small>')
            ->isTime24hr(false)
            ->isEnableSeconds()
            ->setColumns(4);
        $datetimeHour12 = FieldGenerator::datetime('datetimeHour12')
            ->setLabel('12-hour')
            ->isTime24hr(false)
            ->setColumns(4);

        $hierarchyFieldsTab = FieldGenerator::tab('Hierarchy');

        $hierarchyPanel = FieldGenerator::panel('HIERARCHY')->setIcon('input-search');
        $hierarchySwitch = FieldGenerator::switch('hierarchySwitch')
            ->setLabel('On checked <small class="text-muted">data-hf-parent</small>')
            ->setHtmlAttribute('data-hf-parent', 'hierarchySwitch')
            ->setColumns(6);
        $hierarchySwitchChild = FieldGenerator::text('hierarchySwitchChild')
            ->setLabel('Child')
            ->setHtmlAttribute('data-hf-child', 'hierarchySwitch')
            ->setColumns(6);
        $hierarchyRow1 = FieldGenerator::row();
        $hierarchySwitchChecked = FieldGenerator::switch('hierarchySwitchChecked')
            ->setLabel('On checked <small class="text-muted">checked by default</small>')
            ->isChecked()
            ->setHtmlAttribute('data-hf-parent', 'hierarchySwitchChecked')
            ->setColumns(6);
        $hierarchySwitchCheckedChild = FieldGenerator::date('hierarchySwitchCheckedChild')
            ->setLabel('Child date')
            ->isRequired()
            ->setHtmlAttribute('data-hf-child', 'hierarchySwitchChecked')
            ->setColumns(6);
        $hierarchyRow2 = FieldGenerator::row();
        $hierarchySwitchShow = FieldGenerator::switch('hierarchySwitchShow')
            ->setLabel('Show mode <small class="text-muted">data-hf-show + keep-value</small>')
            ->setHtmlAttribute('data-hf-parent', 'hierarchySwitchShow')
            ->setColumns(6);
        $hierarchySwitchShowChild = FieldGenerator::text('hierarchySwitchShowChild')
            ->setLabel('Child')
            ->setHtmlAttribute('data-hf-child', 'hierarchySwitchShow')
            ->setHtmlAttribute('data-hf-show', 'true')
            ->setHtmlAttribute('data-hf-keep-value', 'true')
            ->isRequired()
            ->setColumns(6);
        $hierarchyRow3 = FieldGenerator::row();
        $hierarchySwitchDefaultValue = FieldGenerator::switch('hierarchySwitchDefaultValue')
            ->setLabel('Default on show <small class="text-muted">data-hf-default-value</small>')
            ->setHtmlAttribute('data-hf-parent', 'hierarchySwitchDefaultValue')
            ->setColumns(6);
        $hierarchySwitchDefaultValueChild = FieldGenerator::text('hierarchySwitchDefaultValueChild')
            ->setLabel('Child')
            ->setHtmlAttribute('data-hf-child', 'hierarchySwitchDefaultValue')
            ->setHtmlAttribute('data-hf-default-value', 'This is the default value')
            ->setColumns(6);
        $hierarchyRow4 = FieldGenerator::row();
        $hierarchySwitchInvertValue = FieldGenerator::switch('hierarchySwitchInvertValue')
            ->setLabel('On unchecked <small class="text-muted">data-hf-negate</small>')
            ->isChecked()
            ->setHtmlAttribute('data-hf-parent', 'hierarchySwitchInvertValue')
            ->setColumns(6);
        $hierarchySwitchInvertValueChild = FieldGenerator::text('hierarchySwitchInvertValueChild')
            ->setLabel('Child')
            ->setHtmlAttribute('data-hf-child', 'hierarchySwitchInvertValue')
            ->setHtmlAttribute('data-hf-negate', 'true')
            ->setColumns(6);
        $hierarchyRow5 = FieldGenerator::row();
        $hierarchyText = FieldGenerator::text('hierarchyText')
            ->setLabel('Non-empty parent')
            ->setHtmlAttribute('data-hf-parent', 'hierarchyText')
            ->setColumns(6);
        $hierarchyTextChild = FieldGenerator::text('hierarchyTextChild')
            ->setLabel('Child')
            ->setHtmlAttribute('data-hf-child', 'hierarchyText')
            ->setColumns(6);
        $hierarchyRow6 = FieldGenerator::row();
        $hierarchyTextCustomValue = FieldGenerator::text('hierarchyTextCustomValue')
            ->setLabel('Match value <small class="text-muted">data-hf-parent-value</small>')
            ->setHelp('Type exactly <code>ShOw Me</code> to show the child.')
            ->setHtmlAttribute('data-hf-parent', 'hierarchyTextCustomValue')
            ->setColumns(6);
        $hierarchyTextCustomValueChild = FieldGenerator::text('hierarchyTextCustomValueChild')
            ->setLabel('Child')
            ->setHtmlAttribute('data-hf-child', 'hierarchyTextCustomValue')
            ->setHtmlAttribute('data-hf-parent-value', 'ShOw Me')
            ->setColumns(6);
        $hierarchyRow7 = FieldGenerator::row();
        $hierarchyKeepValue = FieldGenerator::switch('hierarchyKeepValue')
            ->setLabel('Keep value <small class="text-muted">data-hf-keep-value</small>')
            ->setHtmlAttribute('data-hf-parent', 'hierarchyKeepValue')
            ->setColumns(6);
        $hierarchyKeepValueChild = FieldGenerator::text('hierarchyKeepValueChild')
            ->setLabel('Child')
            ->setHtmlAttribute('data-hf-child', 'hierarchyKeepValue')
            ->setHtmlAttribute('data-hf-keep-value', 'true')
            ->setColumns(6);
        $hierarchyRow8 = FieldGenerator::row();
        $hierarchySaveValue = FieldGenerator::switch('hierarchySaveValue')
            ->setLabel('Save value <small class="text-muted">data-hf-save-value</small>')
            ->setHtmlAttribute('data-hf-parent', 'hierarchySaveValue')
            ->setColumns(6);
        $hierarchySaveValueChild = FieldGenerator::text('hierarchySaveValueChild')
            ->setLabel('Child')
            ->setHtmlAttribute('data-hf-child', 'hierarchySaveValue')
            ->setHtmlAttribute('data-hf-save-value', 'true')
            ->setColumns(6);

        $mediaFieldsTab = FieldGenerator::tab('Media');

        $mediaPanel = FieldGenerator::panel('MEDIA')->setIcon('input-search');
        $media = FieldGenerator::media('media')
            ->setLabel('Default')
            ->setColumns(4);
        $mediaImage = FieldGenerator::media('mediaImage')
            ->setLabel('Image <small class="text-muted">public_images</small>')
            ->setConf('public_images')
            ->setColumns(4);
        $mediaImageUsers = FieldGenerator::media('mediaImageUsers')
            ->setLabel('User image <small class="text-muted">public_user_images</small>')
            ->setConf('public_user_images')
            ->setColumns(4);
        $mediaWithoutFileManager = FieldGenerator::media('mediaWithoutFileManager')
            ->setLabel('No file manager <small class="text-muted">displayFileManager(false)</small>')
            ->displayFileManager(false)
            ->setColumns(4);
        $mediaWithCrop = FieldGenerator::media('mediaWithCrop')
            ->setLabel('Crop <small class="text-muted">allowCrop</small>')
            ->allowCrop()
            ->setColumns(4);

        $maskFieldsTab = FieldGenerator::tab('Mask');

        $maskPanel = FieldGenerator::panel('MASK')->setIcon('input-search');
        $maskPhone = FieldGenerator::mask('maskPhone')
            ->setLabel('Phone <small class="text-muted">+34 600 000 000</small>')
            ->setHelp('Fixed prefix. No <code>_</code> guide (default lazy).')
            ->setPattern('+34 000 000 000')
            ->setColumns(4);
        $maskPlate = FieldGenerator::mask('maskPlate')
            ->setLabel('Plate <small class="text-muted">uppercase + unmask</small>')
            ->setHelp('Type a plate; on submit the space is dropped from the saved value.')
            ->setPattern('0000 aaa')
            ->isUppercase()
            ->isUnmask()
            ->setColumns(4);
        $maskPin = FieldGenerator::mask('maskPin')
            ->setLabel('PIN <small class="text-muted">visible guide</small>')
            ->setHelp('Empty slots show <code>_</code>. New digits replace in place.')
            ->setPattern('000000')
            ->isOverwrite()
            ->setMaskPlaceholder('_')
            ->isLazy(false)
            ->setColumns(4);
        $maskDate = FieldGenerator::mask('maskDate')
            ->setLabel('Date <small class="text-muted">eager separators</small>')
            ->setHelp('<code>/</code> is inserted as you type (<code>eager: append</code>).')
            ->setPattern('00/00/0000')
            ->setEager(MaskField::MASK_EAGER_APPEND)
            ->setMaskPlaceholder('_')
            ->isLazy(false)
            ->setColumns(4);
        $maskSecure = FieldGenerator::mask('maskSecure')
            ->setLabel('Secure display <small class="text-muted">######</small>')
            ->setHelp('Digits are real but shown as <code>#</code> (<code>displayChar</code>).')
            ->setPattern('000000')
            ->setDisplayChar('#')
            ->isLazy(false)
            ->setOverwrite(MaskField::MASK_OVERWRITE_SHIFT)
            ->setColumns(4);
        $maskNumber = FieldGenerator::mask('maskNumber')
            ->setLabel('Amount ES <small class="text-muted">1.234,56</small>')
            ->setHelp('Thousands <code>.</code>, decimals <code>,</code>, range 0–999999.99.')
            ->setMaskType(MaskField::MASK_TYPE_NUMBER)
            ->setScale(2)
            ->setRadix(',')
            ->setThousandsSeparator('.')
            ->setMapToRadix(['.'])
            ->isPadFractionalZeros()
            ->setAutofix()
            ->setMaskMin(0)
            ->setMaskMax(999999.99)
            ->setColumns(4);
        $maskPrepareDigits = FieldGenerator::mask('maskPrepareDigits')
            ->setLabel('Paste cleanup <small class="text-muted">phone</small>')
            ->setHelp('Paste <code>34600000000</code> or <code>+34 600-000-000</code>.')
            ->setPattern('+34 000 000 000')
            ->setPrepare([MaskField::PREPARE_STRIP_SPACES, MaskField::PREPARE_DIGITS_ONLY])
            ->setColumns(4);
        $maskDefinitions = FieldGenerator::mask('maskDefinitions')
            ->setLabel('Custom slots <small class="text-muted">#hhhhhh</small>')
            ->setHelp('Literal <code>#</code> + 6 hex slots. Lowercase → uppercase.')
            ->setPattern('\\#hhhhhh')
            ->setDefinitions(['h' => '[0-9A-Fa-f]'])
            ->isUppercase()
            ->setMaskPlaceholder('_')
            ->isLazy(false)
            ->setColumns(4);

        $signatureFieldsTab = FieldGenerator::tab('Signature');

        $signaturePanel = FieldGenerator::panel('UI')->setIcon('input-search');
        $signature = FieldGenerator::signature('signature')
            ->setLabel('Default')
            ->setColumns(4);
        $signatureNoUndo = FieldGenerator::signature('signatureNoUndo')
            ->setLabel('No undo <small class="text-muted">showUndo(false)</small>')
            ->showUndo(false)
            ->setColumns(4);
        $signatureNoClear = FieldGenerator::signature('signatureNoClear')
            ->setLabel('No clear <small class="text-muted">showClear(false)</small>')
            ->showClear(false)
            ->setColumns(4);

        $signatureStrokePanel = FieldGenerator::panel('STROKE')->setIcon('input-search');
        $signaturePenColor = FieldGenerator::signature('signaturePenColor')
            ->setLabel('Pen color <small class="text-muted">#276cb8</small>')
            ->setPenColor('#276cb8')
            ->setColumns(4);
        $signatureThick = FieldGenerator::signature('signatureThick')
            ->setLabel('Thick stroke <small class="text-muted">min 2 / max 8</small>')
            ->setMinWidth(2)
            ->setMaxWidth(8)
            ->setDotSize(5)
            ->setColumns(4);
        $signatureBackground = FieldGenerator::signature('signatureBackground')
            ->setLabel('Background <small class="text-muted">#f0f4f8</small>')
            ->setBackgroundColor('#f0f4f8')
            ->setPenColor('#1a1a2e')
            ->setColumns(4);

        $signatureExportPanel = FieldGenerator::panel('EXPORT')->setIcon('input-search');
        $signatureJpeg = FieldGenerator::signature('signatureJpeg')
            ->setLabel('JPEG <small class="text-muted">q=0.7</small>')
            ->setBackgroundColor('rgb(255,255,255)')
            ->setFormat(SignatureField::FORMAT_JPEG)
            ->setQuality(0.7)
            ->showInput()
            ->setColumns(6);
        $signatureSvg = FieldGenerator::signature('signatureSvg')
            ->setLabel('SVG')
            ->setFormat(SignatureField::FORMAT_SVG)
            ->showInput()
            ->setColumns(6);

        $sliderFieldsTab = FieldGenerator::tab('Slider');

        $sliderPanel = FieldGenerator::panel('BASIC')->setIcon('input-search');
        $slider = FieldGenerator::integer('slider')
            ->setLabel('Default <small class="text-muted">3–8</small>')
            ->isSlider()
            ->setMin(3)
            ->setMax(8)
            ->setColumns(4);
        $sliderFloat = FieldGenerator::float('sliderFloat')
            ->setLabel('Float + pips <small class="text-muted">0–10 / 0.5</small>')
            ->isSlider()
            ->setMin(0)
            ->setMax(10)
            ->setStep(0.5)
            ->sliderPips()
            ->setColumns(4);
        $sliderShowInput = FieldGenerator::integer('sliderShowInput')
            ->setLabel('Show input <small class="text-muted">sliderShowInput</small>')
            ->isSlider()
            ->setMin(0)
            ->setMax(100)
            ->sliderShowInput()
            ->setColumns(4);

        $sliderConnectPanel = FieldGenerator::panel('CONNECT')->setIcon('input-search');
        $sliderConnectUpper = FieldGenerator::integer('sliderConnectUpper')
            ->setLabel('Connect upper <small class="text-muted">sliderConnectUpper</small>')
            ->isSlider()
            ->setMin(0)
            ->setMax(100)
            ->sliderConnectUpper()
            ->setColumns(4);
        $sliderNoTooltip = FieldGenerator::integer('sliderNoTooltip')
            ->setLabel('No tooltips <small class="text-muted">sliderShowTooltip(false)</small>')
            ->isSlider()
            ->setMin(0)
            ->setMax(100)
            ->sliderShowTooltip(false)
            ->setColumns(4);
        $sliderRtl = FieldGenerator::integer('sliderRtl')
            ->setLabel('RTL')
            ->isSlider()
            ->setMin(0)
            ->setMax(100)
            ->setSliderDirection(FloatField::SLIDER_DIRECTION_RTL)
            ->setColumns(4);

        $sliderLayoutPanel = FieldGenerator::panel('LAYOUT')->setIcon('input-search');
        $sliderVertical = FieldGenerator::integer('sliderVertical')
            ->setLabel('Vertical <small class="text-muted">200px</small>')
            ->isSlider()
            ->setMin(0)
            ->setMax(100)
            ->setSliderOrientation(FloatField::SLIDER_ORIENTATION_VERTICAL)
            ->setSliderDirection(FloatField::SLIDER_DIRECTION_RTL)
            ->setSliderHeight('200px')
            ->setColumns(4);
        $sliderSnap = FieldGenerator::float('sliderSnap')
            ->setLabel('Snap <small class="text-muted">0 / 25 / 70 / 100</small>')
            ->isSlider()
            ->isSliderSnap([0, 25, 70, 100])
            ->setColumns(4);
        $sliderBehaviour = FieldGenerator::integer('sliderBehaviour')
            ->setLabel('No tap <small class="text-muted">drag only</small>')
            ->isSlider()
            ->setMin(0)
            ->setMax(100)
            ->setSliderBehaviour('drag')
            ->setColumns(4);

        $slugFieldsTab = FieldGenerator::tab('Slug');

        $slugPanel = FieldGenerator::panel('BASIC')->setIcon('input-search');
        $slugTarget = FieldGenerator::text('slugTarget')
            ->setLabel('Target')
            ->setColumns(6);
        $slug = FieldGenerator::slug('slug')
            ->setLabel('Slug')
            ->setTarget('slugTarget')
            ->setColumns(6);

        $slugConfirmPanel = FieldGenerator::panel('CONFIRM')->setIcon('input-search');
        $slugConfirm = FieldGenerator::slug('slugConfirm')
            ->setLabel('Unlock confirm <small class="text-muted">setConfirmText</small>')
            ->setTarget('slugTarget')
            ->setConfirmText('If the slug is not valid or contains special characters or spaces, it will not work.<br /><strong>Are you sure you want to overwrite?</strong>')
            ->setColumns(6);

        $slugOptionsPanel = FieldGenerator::panel('OPTIONS')->setIcon('input-search');
        $slugLocaleEs = FieldGenerator::slug('slugLocaleEs')
            ->setLabel('Locale es <small class="text-muted">underscore</small>')
            ->setTarget('slugTarget')
            ->setLocale('es')
            ->setReplacement('_')
            ->setColumns(4);
        $slugKeepCase = FieldGenerator::slug('slugKeepCase')
            ->setLabel('Keep case <small class="text-muted">not strict</small>')
            ->setTarget('slugTarget')
            ->isLower(false)
            ->isStrict(false)
            ->setColumns(4);
        $slugKeepDots = FieldGenerator::slug('slugKeepDots')
            ->setLabel('Keep dots <small class="text-muted">setRemove</small>')
            ->setTarget('slugTarget')
            ->setRemove('[^A-Za-z0-9.\\s-]')
            ->isStrict(false)
            ->setColumns(4);

        $texteditorFieldsTab = FieldGenerator::tab('Text editor');

        $texteditorPanel = FieldGenerator::panel('BASIC')->setIcon('input-search');
        $texteditor = FieldGenerator::texteditor('texteditor')
            ->setLabel('Default <small class="text-muted">full</small>')
            ->setColumns(4);
        $texteditorBasic = FieldGenerator::texteditor('texteditorBasic')
            ->setLabel('Basic preset <small class="text-muted">PRESET_BASIC</small>')
            ->setPreset(TextEditorField::PRESET_BASIC)
            ->setColumns(4);
        $texteditorPlaceholder = FieldGenerator::texteditor('texteditorPlaceholder')
            ->setLabel('Placeholder')
            ->setPlaceholder('Write something…')
            ->setColumns(4);
        $texteditorSizePanel = FieldGenerator::panel('SIZE')->setIcon('input-search');
        $texteditorFixedHeight = FieldGenerator::texteditor('texteditorFixedHeight')
            ->setLabel('Fixed height <small class="text-muted">250px</small>')
            ->setHeight(250)
            ->setColumns(4);
        $texteditorAutoresize = FieldGenerator::texteditor('texteditorAutoresize')
            ->setLabel('Autoresize <small class="text-muted">min 150 / max 250</small>')
            ->setMinHeight(150)
            ->setMaxHeight(250)
            ->isResizeable()
            ->setColumns(4);
        $texteditorResizeBoth = FieldGenerator::texteditor('texteditorResizeBoth')
            ->setLabel('Resize both <small class="text-muted">RESIZE_BOTH</small>')
            ->setResize(TextEditorField::RESIZE_BOTH)
            ->setColumns(4);

        $texteditorToolbarPanel = FieldGenerator::panel('TOOLBAR')->setIcon('input-search');
        $texteditorCustomToolbar = FieldGenerator::texteditor('texteditorCustomToolbar')
            ->setLabel('Custom toolbar <small class="text-muted">setToolbar</small>')
            ->setToolbar('undo redo | bold italic underline | bullist numlist | link | emoticons')
            ->setColumns(4);
        $texteditorStickyToolbar = FieldGenerator::texteditor('texteditorStickyToolbar')
            ->setLabel('Sticky toolbar <small class="text-muted">wrap mode</small>')
            ->setToolbarMode(TextEditorField::TOOLBAR_MODE_WRAP)
            ->isToolbarSticky()
            ->setMinHeight(500)
            ->setColumns(4);
        $texteditorMenubar = FieldGenerator::texteditor('texteditorMenubar')
            ->setLabel('Menubar <small class="text-muted">file edit insert</small>')
            ->setMenubar('file edit insert')
            ->setMinHeight(250)
            ->setColumns(4);

        $texteditorQuickbarsPanel = FieldGenerator::panel('MENUBAR & QUICKBARS')->setIcon('input-search');
        $texteditorSelectionQuickbar = FieldGenerator::texteditor('texteditorSelectionQuickbar')
            ->setLabel('Selection quickbar <small class="text-muted">setQuickbarsSelectionToolbar</small>')
            ->setQuickbarsSelectionToolbar('bold italic underline | quicklink')
            ->setColumns(6);
        $texteditorInsertQuickbar = FieldGenerator::texteditor('texteditorInsertQuickbar')
            ->setLabel('Insert quickbar <small class="text-muted">setQuickbarsInsertToolbar</small>')
            ->setQuickbarsInsertToolbar('quickimage quicktable')
            ->setColumns(6);

        $texteditorBehaviorPanel = FieldGenerator::panel('BEHAVIOR')->setIcon('input-search');
        $texteditorPasteAsText = FieldGenerator::texteditor('texteditorPasteAsText')
            ->setLabel('Paste as text <small class="text-muted">no spellcheck</small>')
            ->isPasteAsText()
            ->isSpellcheck(false)
            ->setColumns(4);
        $texteditorContextMenu = FieldGenerator::texteditor('texteditorContextMenu')
            ->setLabel('Context menu <small class="text-muted">convert URLs</small>')
            ->setContextMenu('link image table')
            ->isConvertUrls()
            ->setColumns(4);
        $texteditorReadonly = FieldGenerator::texteditor('texteditorReadonly')
            ->setLabel('Readonly')
            ->isReadonly()
            ->setColumns(4);

        $texteditorChromePanel = FieldGenerator::panel('STATE & PLUGINS')->setIcon('input-search');
        $texteditorNoStatusbar = FieldGenerator::texteditor('texteditorNoStatusbar')
            ->setLabel('No statusbar')
            ->showStatusbar(false)
            ->setColumns(4);
        $texteditorNoElementPath = FieldGenerator::texteditor('texteditorNoElementPath')
            ->setLabel('No element path <small class="text-muted">statusbar kept</small>')
            ->showElementPath(false)
            ->setColumns(4);
        $texteditorReducedPlugins = FieldGenerator::texteditor('texteditorReducedPlugins')
            ->setLabel('Reduced plugins <small class="text-muted">+ content style</small>')
            ->setPlugins(['lists', 'link', 'autoresize'])
            ->setContentStyle('body { font-family: "Courier New", monospace !important; }')
            ->setColumns(4);
        yield $basicFieldsTab;
        yield $idPanel;
        yield $id;
        yield $textPanel;
        yield $text;
        yield $textRequired;
        yield $textHelp;
        yield $textRow;
        yield $email;
        yield $phone;
        yield $url;
        yield $password;
        yield $confirmPanel;
        yield $confirmPassword;
        yield $confirmEmail;
        yield $numbersPanel;
        yield $integerNumber;
        yield $integerPositive;
        yield $floatNumber;
        yield $floatPositive;
        yield $floatStepHalf;
        yield $percent;
        yield $money;
        yield $moneyCustomCurrency;
        yield $checkboxPanel;
        yield $checkbox;
        yield $switch;
        yield $filesPanel;
        yield $file;
        yield $filePDF;
        yield $image;
        yield $textareaPanel;
        yield $textarea;
        yield $textareaBounds;
        yield $textareaNoAutogrow;
        yield $textareaStatic;
        yield $choicePanel;
        yield $choice;
        yield $choiceExpanded;
        yield $choiceExpandedRequired;
        yield $choiceRow;
        yield $choiceMultiple;
        yield $choiceMultipleExpanded;

        yield $codeeditorFieldsTab;
        yield $codeeditorPanel;
        yield $codeeditor;
        yield $codeeditorPhpGithub;
        yield $codeeditorPlaceholder;
        yield $codeeditorSizePanel;
        yield $codeeditorBounds;
        yield $codeeditorIndentSpaces;
        yield $codeeditorDisplayPanel;
        yield $codeeditorWrap;
        yield $codeeditorPrintMargin;
        yield $codeeditorMinimalUi;
        yield $codeeditorStatePanel;
        yield $codeeditorReadonly;

        yield $collectionFieldsTab;
        yield $collectionPanel;
        yield $array;
        yield $collection;
        yield $collectionOptionsPanel;
        yield $collectionNoAdd;
        yield $collectionNoDelete;
        yield $collectionLabels;
        yield $collectionPluginsPanel;
        yield $collectionPlugins;

        yield $selectFieldsTab;
        yield $selectBasicPanel;
        yield $selectDefault;
        yield $selectEnum;
        yield $selectRequired;
        yield $selectCreatePanel;
        yield $selectCreate;
        yield $selectCreateOnBlur;
        yield $selectHtml;
        yield $selectLimitsPanel;
        yield $selectMultiple;
        yield $selectMaxOptions;
        yield $selectMaxItems;
        yield $selectBehaviorPanel;
        yield $selectOnTab;
        yield $selectDiacritics;
        yield $selectNoClear;
        yield $selectNoDropdown;
        yield $selectMultipleExtrasPanel;
        yield $selectCloseAfterSelect;
        yield $selectHideSelected;
        yield $selectCheckbox;
        yield $selectNoRemove;
        yield $selectDuplicates;
        yield $selectAssociationPanel;
        yield $selectRole;
        yield $selectUser;

        yield $colorFieldsTab;
        yield $colorPanel;
        yield $color;
        yield $colorFlat;
        yield $colorAlpha;
        yield $colorButtons;
        yield $colorPalettePanel;
        yield $colorPalette;
        yield $colorPaletteOnly;
        yield $colorFlatPalette;
        yield $colorFlatPaletteOnly;
        yield $colorCustomPalette;
        yield $colorTogglePalette;
        yield $colorSelection;

        yield $dateFieldsTab;
        yield $datePanel;
        yield $date;
        yield $datetime;
        yield $time;
        yield $dateDefaultValuesPanel;
        yield $dateDefaultValue;
        yield $datetimeDefaultValue;
        yield $timeDefaultValue;
        yield $dateMinMaxPanel;
        yield $dateMinMax;
        yield $datetimeMinMax;
        yield $timeMinMax;
        yield $dateEnableDisablePanel;
        yield $dateEnable;
        yield $dateDisable;
        yield $dateMultiplePanel;
        yield $dateMultiple;
        yield $datetimeMultiple;
        yield $dateMultipleRow;
        yield $dateMultipleCustom;
        yield $datetimeMultipleCustom;
        yield $dateRangePanel;
        yield $dateRange;
        yield $datetimeRange;
        yield $dateRangeCustom;
        yield $dateInlinePanel;
        yield $dateInline;
        yield $datetimeInline;
        yield $timeInline;
        yield $dateCalendarUiPanel;
        yield $dateCalendarUi;
        yield $dateStatic;
        yield $datePickerPanel;
        yield $datePickerOpts;
        yield $dateAltFormat;
        yield $dateNative;
        yield $dateClockPanel;
        yield $datetimeClock;
        yield $timeHour12;
        yield $datetimeHour12;

        yield $hierarchyFieldsTab;
        yield $hierarchyPanel;
        yield $hierarchySwitch;
        yield $hierarchySwitchChild;
        yield $hierarchyRow1;
        yield $hierarchySwitchChecked;
        yield $hierarchySwitchCheckedChild;
        yield $hierarchyRow2;
        yield $hierarchySwitchShow;
        yield $hierarchySwitchShowChild;
        yield $hierarchyRow3;
        yield $hierarchySwitchDefaultValue;
        yield $hierarchySwitchDefaultValueChild;
        yield $hierarchyRow4;
        yield $hierarchySwitchInvertValue;
        yield $hierarchySwitchInvertValueChild;
        yield $hierarchyRow5;
        yield $hierarchyText;
        yield $hierarchyTextChild;
        yield $hierarchyRow6;
        yield $hierarchyTextCustomValue;
        yield $hierarchyTextCustomValueChild;
        yield $hierarchyRow7;
        yield $hierarchyKeepValue;
        yield $hierarchyKeepValueChild;
        yield $hierarchyRow8;
        yield $hierarchySaveValue;
        yield $hierarchySaveValueChild;

        yield $mediaFieldsTab;
        yield $mediaPanel;
        yield $media;
        yield $mediaImage;
        yield $mediaImageUsers;
        yield $mediaWithoutFileManager;
        yield $mediaWithCrop;

        yield $maskFieldsTab;
        yield $maskPanel;
        yield $maskPhone;
        yield $maskPlate;
        yield $maskPin;
        yield $maskDate;
        yield $maskSecure;
        yield $maskNumber;
        yield $maskPrepareDigits;
        yield $maskDefinitions;

        yield $signatureFieldsTab;
        yield $signaturePanel;
        yield $signature;
        yield $signatureNoUndo;
        yield $signatureNoClear;
        yield $signatureStrokePanel;
        yield $signaturePenColor;
        yield $signatureThick;
        yield $signatureBackground;
        yield $signatureExportPanel;
        yield $signatureJpeg;
        yield $signatureSvg;

        yield $sliderFieldsTab;
        yield $sliderPanel;
        yield $slider;
        yield $sliderFloat;
        yield $sliderShowInput;
        yield $sliderConnectPanel;
        yield $sliderConnectUpper;
        yield $sliderNoTooltip;
        yield $sliderRtl;
        yield $sliderLayoutPanel;
        yield $sliderVertical;
        yield $sliderSnap;
        yield $sliderBehaviour;

        yield $slugFieldsTab;
        yield $slugPanel;
        yield $slugTarget;
        yield $slug;
        yield $slugConfirmPanel;
        yield $slugConfirm;
        yield $slugOptionsPanel;
        yield $slugLocaleEs;
        yield $slugKeepCase;
        yield $slugKeepDots;

        yield $texteditorFieldsTab;
        yield $texteditorPanel;
        yield $texteditor;
        yield $texteditorBasic;
        yield $texteditorPlaceholder;
        yield $texteditorSizePanel;
        yield $texteditorFixedHeight;
        yield $texteditorAutoresize;
        yield $texteditorResizeBoth;
        yield $texteditorToolbarPanel;
        yield $texteditorCustomToolbar;
        yield $texteditorStickyToolbar;
        yield $texteditorMenubar;
        yield $texteditorQuickbarsPanel;
        yield $texteditorSelectionQuickbar;
        yield $texteditorInsertQuickbar;
        yield $texteditorBehaviorPanel;
        yield $texteditorPasteAsText;
        yield $texteditorContextMenu;
        yield $texteditorReadonly;
        yield $texteditorChromePanel;
        yield $texteditorNoStatusbar;
        yield $texteditorNoElementPath;
        yield $texteditorReducedPlugins;
    }
}
