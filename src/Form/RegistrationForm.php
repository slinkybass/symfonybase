<?php

namespace App\Form;

use App\Entity\User;
use App\Field\FieldGenerator;
use App\Service\ConfigService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationForm extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly TranslatorInterface $translator,
        private readonly ConfigService $configService,
        private readonly FormGenerator $formGenerator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $config = $this->configService->get();

        $fields = [];
        $fields[] = FieldGenerator::text('name')
            ->setLabel('entities.user.fields.name')
            ->setPlaceholder('entities.user.fields.name')
            ->setHtmlAttribute('autofocus', true)
            ->setColumns(2);
        $fields[] = FieldGenerator::text('lastname')
            ->setLabel('entities.user.fields.lastname')
            ->setPlaceholder('entities.user.fields.lastname')
            ->setColumns(3);
        $fields[] = FieldGenerator::email('email')
            ->setLabel('entities.user.fields.email')
            ->setPlaceholder('entities.user.fields.email')
            ->setColumns(4);
        $fields[] = FieldGenerator::phone('phone')
            ->setLabel('entities.user.fields.phone')
            ->setPlaceholder('entities.user.fields.phone')
            ->setColumns(3);
        $fields[] = FieldGenerator::date('birthdate')
            ->setLabel('entities.user.fields.birthdate')
            ->setPlaceholder('entities.user.fields.birthdate')
            ->setColumns(2);
        $fields[] = FieldGenerator::enum('gender')
            ->setLabel('entities.user.fields.gender')
            ->setPlaceholder('entities.user.fields.gender')
            ->setColumns(2);
        $fields[] = FieldGenerator::password('plainPassword')
            ->isRepeated()
            ->renderSwitch()
            ->renderGenerator()
            ->setFirstLabel('entities.user.fields.password')
            ->setFirstPlaceholder('entities.user.fields.password')
            ->setSecondLabel('entities.user.fields.repeatPassword')
            ->setSecondPlaceholder('entities.user.fields.repeatPassword')
            ->setMinLength(8)
            ->isMapped(false);
        $termsLabel = $config->privacyText ? 'public.register.acceptTermsUrl' : 'public.register.acceptTerms';
        $termsLabel = $this->translator->trans($termsLabel, ['%url%' => $this->router->generate('privacy')]);
        $fields[] = FieldGenerator::switch('acceptTerms')
            ->setLabel($termsLabel)
            ->setFormTypeOption('label_html', true)
            ->isMapped(false);

        $builder = $this->formGenerator->getFormBuilder($builder, $fields, User::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
