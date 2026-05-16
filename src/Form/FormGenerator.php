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

            if ($entityClass) {
                $options = $this->resolveDoctrineOptions($options, $fieldDto->getProperty(), $entityClass);
            }

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
     * Adds the ea-autocomplete attribute when the field uses TomSelect outside of EasyAdmin context.
     *
     * @param array  $options the current form type options
     * @param object $field   the field definition
     *
     * @return array<string, mixed>
     */
    private function resolveAutocomplete(array $options, object $field): array
    {
        $fieldDto = $field->getAsDto();

        if (EasyChoiceField::WIDGET_AUTOCOMPLETE === $fieldDto->getCustomOption(EasyChoiceField::OPTION_WIDGET)) {
            $options['attr']['data-ea-widget'] = 'ea-autocomplete';
        }

        return $options;
    }
}
