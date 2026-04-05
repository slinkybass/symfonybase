<?php

namespace App\Form;

use App\Entity\User;
use App\Field\FieldGenerator;
use App\Service\ConfigService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationForm extends AbstractType
{
    private Request $request;
    private RouterInterface $router;
    private TranslatorInterface $translator;
    private ConfigService $configService;

    public function __construct(RequestStack $requestStack, RouterInterface $router, TranslatorInterface $translator, ConfigService $configService)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->translator = $translator;
        $this->configService = $configService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $config = $this->configService->get();

        $fields = [];
        $fields[] = FieldGenerator::text('name')
            ->setLabel('entities.user.fields.name')
            ->setPlaceholder('entities.user.fields.name')
            ->setHtmlAttribute('autofocus', true);
        $fields[] = FieldGenerator::email('email')
            ->setLabel('entities.user.fields.email')
            ->setPlaceholder('entities.user.fields.email');
        $fields[] = FieldGenerator::password('plainPassword')
            ->isRepeated()
            ->renderSwitch()
            ->renderGenerator()
            ->setFirstLabel('entities.user.fields.password')
            ->setFirstPlaceholder('entities.user.fields.password')
            ->setSecondLabel('entities.user.fields.repeatPassword')
            ->setSecondPlaceholder('entities.user.fields.repeatPassword')
            ->isMapped(false);
        $termsLabel = $config->privacyText ? 'public.register.acceptTermsUrl' : 'public.register.acceptTerms';
        $termsLabel = $this->translator->trans($termsLabel, ['%url%' => $this->router->generate('privacy')]);
        $fields[] = FieldGenerator::switch('acceptTerms')
            ->setLabel($termsLabel)
            ->setFormTypeOption('label_html', true)
            ->isMapped(false);

        $builder = FormGenerator::getFormBuilder($builder, $fields);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
