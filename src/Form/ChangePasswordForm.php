<?php

namespace App\Form;

use App\Field\FieldGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordForm extends AbstractType
{
    public function __construct(
        private readonly FormGenerator $formGenerator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->formGenerator->getFormBuilder($builder, [
            FieldGenerator::password('plainPassword')
                ->isRepeated()
                ->renderSwitch()
                ->renderGenerator()
                ->setFirstLabel('entities.user.fields.password')
                ->setFirstPlaceholder('entities.user.fields.password')
                ->setSecondLabel('entities.user.fields.repeatPassword')
                ->setSecondPlaceholder('entities.user.fields.repeatPassword')
                ->setFormTypeOption('first_options.attr.autofocus', true)
                ->isMapped(false),
        ]);
    }
}
