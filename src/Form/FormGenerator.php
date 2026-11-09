<?php

namespace App\Form;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\MappingException;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField as EasyChoiceField;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Maps `FieldGenerator` / EasyAdmin-style field objects onto a `FormBuilderInterface` (Doctrine metadata + TomSelect widget hints).
 */
class FormGenerator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @param array<int, object> $fields      instances exposing `getAsDto()` (see `FieldGenerator`)
     * @param class-string|null  $entityClass when set, `required` and backed enum `class` are inferred from Doctrine metadata
     * @param bool               $submitField append a `save` submit button
     */
    public function getFormBuilder(
        FormBuilderInterface $builder,
        array $fields,
        ?string $entityClass = null,
        bool $submitField = false,
    ): FormBuilderInterface {
        foreach ($fields as $field) {
            $fieldDto = $field->getAsDto();
            $options = $fieldDto->getFormTypeOptions();
            $options['label'] = $fieldDto->getLabel();
            // EasyAdmin CommonPostConfigurator sets this; FormGenerator is outside that pipeline.
            $options['label_html'] ??= true;

            $htmlAttributes = $fieldDto->getHtmlAttributes();
            if ([] !== $htmlAttributes) {
                $options['attr'] = array_merge($options['attr'] ?? [], $htmlAttributes);
            }

            // EasyAdmin reads columns from FieldDto; outside CRUD pass them for the form theme.
            $columns = $fieldDto->getColumns() ?? $fieldDto->getDefaultColumns();
            if ($columns) {
                $options['row_attr']['data-ea-columns'] = $columns;
            }

            if ($entityClass) {
                $options = $this->resolveDoctrineOptions($options, $fieldDto->getProperty(), $entityClass);
            }

            $options = $this->resolveChoiceOptions($options, $field);
            $options = $this->resolveAutocomplete($options, $field);

            $builder->add($fieldDto->getProperty(), $fieldDto->getFormType(), $options);
        }

        if ($submitField) {
            $builder->add('save', SubmitType::class);
        }

        return $builder;
    }

    /**
     * @param class-string $entityClass
     *
     * @return array<string, mixed>
     */
    private function resolveDoctrineOptions(array $options, string $property, string $entityClass): array
    {
        try {
            $metadata = $this->em->getClassMetadata($entityClass);
        } catch (MappingException) {
            return $options;
        }

        if ($metadata->hasField($property)) {
            $mapping = $metadata->getFieldMapping($property);

            if (!isset($options['required'])) {
                $options['required'] = !($mapping['nullable'] ?? false);
            }

            if (!isset($options['class']) && isset($mapping['enumType'])) {
                $options['class'] = $mapping['enumType'];
            }
        }

        return $options;
    }

    /**
     * Maps EasyAdmin `ChoiceField` custom options onto Symfony `ChoiceType` options.
     * (In CRUD, `ChoiceConfigurator` does this; FormGenerator runs outside that pipeline.)
     *
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function resolveChoiceOptions(array $options, object $field): array
    {
        $fieldDto = $field->getAsDto();
        if (EasyChoiceField::class !== $fieldDto->getFieldFqcn()) {
            return $options;
        }

        $choices = $fieldDto->getCustomOption(EasyChoiceField::OPTION_CHOICES);
        if (\is_callable($choices)) {
            $choices = $choices(null, $fieldDto);
        }
        if (\is_array($choices)) {
            $options['choices'] ??= $choices;
        }

        $options['multiple'] ??= true === $fieldDto->getCustomOption(EasyChoiceField::OPTION_ALLOW_MULTIPLE_CHOICES);
        $options['expanded'] ??= true === $fieldDto->getCustomOption(EasyChoiceField::OPTION_RENDER_EXPANDED);
        $options['placeholder'] ??= '';

        $preferred = $fieldDto->getCustomOption(EasyChoiceField::OPTION_PREFERRED_CHOICES);
        if (null !== $preferred) {
            $options['preferred_choices'] ??= $preferred;
        }

        return $options;
    }

    /**
     * Adds the ea-autocomplete attribute when the field uses TomSelect outside of EasyAdmin context.
     *
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function resolveAutocomplete(array $options, object $field): array
    {
        $fieldDto = $field->getAsDto();

        if (EasyChoiceField::WIDGET_AUTOCOMPLETE === $fieldDto->getCustomOption(EasyChoiceField::OPTION_WIDGET)) {
            $options['attr'] ??= [];
            $options['attr']['data-ea-widget'] = 'ea-autocomplete';
        }

        return $options;
    }
}
