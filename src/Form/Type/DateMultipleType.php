<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Single-line text field whose model data is a list of date strings; the view value is comma-separated.
 *
 * Useful where multiple ISO-like date tokens are edited as one string and transformed to `string[]`.
 */
class DateMultipleType extends AbstractType
{
    public const SEPARATOR = ', ';

    public function getParent(): string
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $separator = $options['separator'];
        $builder->addModelTransformer(new class($separator) implements DataTransformerInterface {
            public function __construct(private string $separator)
            {
            }

            public function transform(mixed $value): string
            {
                if (!is_array($value)) {
                    return '';
                }

                return implode($this->separator, $value);
            }

            public function reverseTransform(mixed $value): ?array
            {
                if (!is_string($value)) {
                    return null;
                }
                $delimiter = str_contains($this->separator, ',') ? ',' : $this->separator;
                $dates = array_values(array_filter(array_map('trim', explode($delimiter, $value)), static fn (string $item): bool => $item !== ''));

                return empty($dates) ? null : $dates;
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
            'separator' => self::SEPARATOR,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'datemultiple';
    }
}
