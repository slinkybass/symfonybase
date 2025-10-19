<?php

namespace App\Form;

use App\Field\FieldGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordRequestForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fields = [];
        $fields[] = FieldGenerator::email('email')
            ->setLabel('entities.user.fields.email')
            ->setPlaceholder('entities.user.fields.email')
            ->setHtmlAttribute('autofocus', true);

        $builder = FormGenerator::getFormBuilder($builder, $fields);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
