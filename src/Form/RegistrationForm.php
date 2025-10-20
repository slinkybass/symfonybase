<?php

namespace App\Form;

use App\Entity\User;
use App\Field\FieldGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

use function Symfony\Component\Translation\t;

class RegistrationForm extends AbstractType
{
    private Request $request;
    private RouterInterface $router;

    public function __construct(RequestStack $requestStack, RouterInterface $router)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $session = $this->request->getSession();

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
        $termsLabel = $session->get('config')->privacyText ? 'public.register.acceptTermsUrl' : 'public.register.acceptTerms';
        $termsLabel = t($termsLabel, ['%url%' => $this->router->generate('privacy')]);
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
