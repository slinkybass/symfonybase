<?php

namespace App\Form;

use App\Field\FieldGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fields = [];
        $fields[] = FieldGenerator::password('plainPassword')
            ->isRepeated()
            ->renderSwitch()
            ->renderGenerator()
            ->setFirstLabel('entities.user.fields.password')
            ->setFirstPlaceholder('entities.user.fields.password')
            ->setSecondLabel('entities.user.fields.repeatPassword')
            ->setSecondPlaceholder('entities.user.fields.repeatPassword')
            ->setFormTypeOption('first_options.attr.autofocus', true)
            ->isMapped(false);

        $builder = FormGenerator::getFormBuilder($builder, $fields);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
