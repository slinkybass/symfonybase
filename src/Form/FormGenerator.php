<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormGenerator extends AbstractType
{
    public static function getFormBuilder($builder, array $fields, $submitField = null)
    {
        foreach ($fields as $field) {
            $field = $field->getAsDto();

            $options = $field->getFormTypeOptions();
            $options['label'] = $field->getLabel();

            $builder->add($field->getProperty(), $field->getFormType(), $options);
        }
        if ($submitField) {
            $builder->add('save', SubmitType::class);
        }

        return $builder;
    }
}
