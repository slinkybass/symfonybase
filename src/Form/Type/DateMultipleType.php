<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateMultipleType extends AbstractType
{
    public function getParent(): string
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new class implements DataTransformerInterface {
            public function transform($value): string
            {
                if (!is_array($value)) {
                    return '';
                }

                return implode(', ', $value);
            }

            public function reverseTransform($value): array
            {
                if (!is_string($value)) {
                    return [];
                }
                return array_values(array_filter(array_map('trim', explode(',', $value)), fn($item) => $item !== ''));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'allow_add' => false,
            'allow_delete' => false,
            'delete_empty' => false,
            'entry_options' => [],
            'entry_type' => TextType::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'datemultiple';
    }
}
