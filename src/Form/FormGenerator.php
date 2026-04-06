<?php

namespace App\Form;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField as EasyChoiceField;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Builds Symfony Form instances from EasyAdmin-compatible field definitions.
 */
class FormGenerator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Builds a Symfony Form from an array of EasyAdmin-compatible field definitions.
     *
     * @param FormBuilderInterface $builder     the form builder instance
     * @param array                $fields      array of field definitions
     * @param bool                 $submitField whether to append a submit button
     * @param string|null          $entityClass the entity class to resolve Doctrine metadata from
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
     * Resolves required and enum class from Doctrine metadata.
     *
     * @param array  $options     current form type options
     * @param string $property    the field property name
     * @param string $entityClass the entity class
     *
     * @return array<string, mixed>
     */
    private function resolveDoctrineOptions(array $options, string $property, string $entityClass): array
    {
        try {
            $metadata = $this->em->getClassMetadata($entityClass);

            if ($metadata->hasField($property)) {
                $mapping = $metadata->getFieldMapping($property);

                if (!isset($options['required'])) {
                    $options['required'] = !($mapping['nullable'] ?? false);
                }

                if (!isset($options['class']) && isset($mapping['enumType'])) {
                    $options['class'] = $mapping['enumType'];
                }
            }
        } catch (\Exception) {
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
