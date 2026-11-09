<?php

namespace App\Form\Type;

use App\Field\FieldGenerator;
use App\Form\FormGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Demo collection entry: hierarchy (`data-hf-*`) + plugins (date / color / mask).
 *
 * Group names stay stable in the prototype; `form-type-collection` appends `_{index}`
 * per row so parents/children do not leak across entries. Scope is the collection item
 * (see `hierarchy-fields.js` SCOPE_SELECTOR).
 *
 * Relies on sibling CRUD fields loading the same plugin assets on the demo form.
 */
class DemoEntityType extends AbstractType
{
    public function __construct(
        private readonly FormGenerator $formGenerator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->formGenerator->getFormBuilder($builder, [
            // --- Root parent: gates the whole entry ---
            FieldGenerator::switch('enabled')
                ->setLabel('Enable entry <small class="text-muted">data-hf-parent</small>')
                ->setHtmlAttribute('data-hf-parent', 'entryEnabled'),
            FieldGenerator::text('disabledNote')
                ->setLabel('Shown when OFF <small class="text-muted">negate + default-value</small>')
                ->setHtmlAttribute('data-hf-child', 'entryEnabled')
                ->setHtmlAttribute('data-hf-negate', 'true')
                ->setHtmlAttribute('data-hf-default-value', 'Turn the switch on to edit this entry')
                ->isRequired(false)
                ->setColumns(6),

            // --- Children of enabled (also a parent for mode) ---
            FieldGenerator::text('title')
                ->setLabel('Title <small class="text-muted">required + save-value</small>')
                ->isRequired()
                ->setHtmlAttribute('data-hf-child', 'entryEnabled')
                ->setHtmlAttribute('data-hf-save-value', 'true')
                ->setColumns(6),
            FieldGenerator::choice('mode')
                ->setLabel('Mode <small class="text-muted">child of enabled + parent</small>')
                ->setChoices([
                    'Simple' => 'simple',
                    'Advanced' => 'advanced',
                    'Custom' => 'custom',
                ])
                ->isRequired()
                ->setHtmlAttribute('data-hf-child', 'entryEnabled')
                ->setHtmlAttribute('data-hf-parent', 'entryMode')
                ->setHtmlAttribute('data-hf-save-value', 'true')
                ->setColumns(6),

            // --- Mode: simple → locked note (show + keep-value) ---
            FieldGenerator::text('simpleNote')
                ->setLabel('Simple note <small class="text-muted">parent-value=simple; show+keep</small>')
                ->setHtmlAttribute('data-hf-child', 'entryMode')
                ->setHtmlAttribute('data-hf-parent-value', 'simple')
                ->setHtmlAttribute('data-hf-show', 'true')
                ->setHtmlAttribute('data-hf-keep-value', 'true')
                ->setHtmlAttribute('data-hf-default-value', 'Simple mode: field stays visible but locked')
                ->isRequired(false)
                ->setColumns(12),

            // --- Mode: advanced|custom → plugins (date / color / choice) ---
            FieldGenerator::date('date')
                ->setLabel('Date <small class="text-muted">Flatpickr; advanced|custom</small>')
                ->isRequired()
                ->setHtmlAttribute('data-hf-child', 'entryMode')
                ->setHtmlAttribute('data-hf-parent-value', 'advanced|custom')
                ->setHtmlAttribute('data-hf-match', 'any')
                ->setHtmlAttribute('data-hf-save-value', 'true')
                ->setColumns(4),
            FieldGenerator::color('color')
                ->setLabel('Color <small class="text-muted">Spectrum; advanced|custom</small>')
                ->isRequired(false)
                ->setHtmlAttribute('data-hf-child', 'entryMode')
                ->setHtmlAttribute('data-hf-parent-value', 'advanced|custom')
                ->setHtmlAttribute('data-hf-match', 'any')
                ->setHtmlAttribute('data-hf-keep-value', 'true')
                ->setColumns(4),
            FieldGenerator::choice('choice')
                ->setLabel('Choice <small class="text-muted">advanced|custom</small>')
                ->setChoices(['A' => 'a', 'B' => 'b', 'C' => 'c'])
                ->isRequired()
                ->setHtmlAttribute('data-hf-child', 'entryMode')
                ->setHtmlAttribute('data-hf-parent-value', 'advanced|custom')
                ->setHtmlAttribute('data-hf-match', 'any')
                ->setHtmlAttribute('data-hf-save-value', 'true')
                ->setHtmlAttribute('data-hf-parent', 'entryChoice')
                ->setColumns(4),

            // --- Mode: custom only → mask + chain from choice ---
            FieldGenerator::mask('phone')
                ->setLabel('Phone <small class="text-muted">IMask; custom only</small>')
                ->setPattern('+34 000 000 000')
                ->isRequired(false)
                ->setHtmlAttribute('data-hf-child', 'entryMode')
                ->setHtmlAttribute('data-hf-parent-value', 'custom')
                ->setHtmlAttribute('data-hf-save-value', 'true')
                ->setColumns(6),
            FieldGenerator::text('choiceDetail')
                ->setLabel('Detail for B/C <small class="text-muted">child of choice; parent-value b|c</small>')
                ->isRequired(false)
                ->setHtmlAttribute('data-hf-child', 'entryChoice')
                ->setHtmlAttribute('data-hf-parent-value', 'b|c')
                ->setHtmlAttribute('data-hf-match', 'any')
                ->setHtmlAttribute('data-hf-default-value', 'Only when choice is B or C')
                ->setColumns(6),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
