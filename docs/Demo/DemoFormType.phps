<?php

namespace App\Form;

use App\Field\FieldGenerator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemoFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$fields = array();
		$fields[] = FieldGenerator::text('text1')->setLabel('text1')->isRequired()->setColumns(3);
		$fields[] = FieldGenerator::text('text2')->setLabel('text2')->isRequired()->setColumns(3);

		foreach ($fields as $field) {
			$field = $field->getAsDto();

			$options = $field->getFormTypeOptions();
			$options['label'] = $field->getLabel();

			$builder->add($field->getProperty(), $field->getFormType(), $options);
		}
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([]);
	}
}
