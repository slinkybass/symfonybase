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

/**
 * Public registration: profile fields plus repeated `plainPassword` and unmapped terms acceptance.
 *
 * Terms label links to the privacy route when `AppConfig` provides custom privacy HTML.
 */
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

        $termsLabelKey = $config->privacyText ? 'public.register.acceptTermsUrl' : 'public.register.acceptTerms';
        $termsLabel = $this->translator->trans($termsLabelKey, ['%url%' => $this->router->generate('privacy')]);

        $this->formGenerator->getFormBuilder($builder, [
            FieldGenerator::text('name')
                ->setLabel('entities.user.fields.name')
                ->setPlaceholder('entities.user.fields.name')
                ->setHtmlAttribute('autofocus', true)
                ->setColumns(2),
            FieldGenerator::text('lastname')
                ->setLabel('entities.user.fields.lastname')
                ->setPlaceholder('entities.user.fields.lastname')
                ->setColumns(3),
            FieldGenerator::email('email')
                ->setLabel('entities.user.fields.email')
                ->setPlaceholder('entities.user.fields.email')
                ->setColumns(4),
            FieldGenerator::phone('phone')
                ->setLabel('entities.user.fields.phone')
                ->setPlaceholder('entities.user.fields.phone')
                ->setColumns(3),
            FieldGenerator::date('birthdate')
                ->setLabel('entities.user.fields.birthdate')
                ->setPlaceholder('entities.user.fields.birthdate')
                ->setColumns(2),
            FieldGenerator::enum('gender')
                ->setLabel('entities.user.fields.gender')
                ->setPlaceholder('entities.user.fields.gender')
                ->setColumns(2),
            FieldGenerator::password('plainPassword')
                ->isRepeated()
                ->renderSwitch()
                ->renderGenerator()
                ->setFirstLabel('entities.user.fields.password')
                ->setFirstPlaceholder('entities.user.fields.password')
                ->setSecondLabel('entities.user.fields.repeatPassword')
                ->setSecondPlaceholder('entities.user.fields.repeatPassword')
                ->setMinLength(8)
                ->isMapped(false),
            FieldGenerator::switch('acceptTerms')
                ->setLabel($termsLabel)
                ->setFormTypeOption('label_html', true)
                ->isMapped(false),
        ], User::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
