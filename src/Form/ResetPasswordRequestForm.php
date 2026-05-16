<?php

namespace App\Form;

use App\Field\FieldGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * First step of reset password: collect email for SymfonyCasts lookup.
 */
class ResetPasswordRequestForm extends AbstractType
{
    public function __construct(
        private readonly FormGenerator $formGenerator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->formGenerator->getFormBuilder($builder, [
            FieldGenerator::email('email')
                ->setLabel('entities.user.fields.email')
                ->setPlaceholder('entities.user.fields.email')
                ->setHtmlAttribute('autofocus', true),
        ]);
    }
}
