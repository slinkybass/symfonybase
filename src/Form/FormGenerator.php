<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class FormGenerator extends AbstractType
{
    public static function getFormBuilder(FormBuilderInterface $builder, array $fields, bool $submitField = false): FormBuilderInterface
    {
        foreach ($fields as $field) {
            $fieldDto = $field->getAsDto();
            $options = $fieldDto->getFormTypeOptions();
            $options['label'] = $fieldDto->getLabel();

            $builder->add($fieldDto->getProperty(), $fieldDto->getFormType(), $options);
        }
        if ($submitField) {
            $builder->add('save', SubmitType::class);
        }

        return $builder;
    }
}
