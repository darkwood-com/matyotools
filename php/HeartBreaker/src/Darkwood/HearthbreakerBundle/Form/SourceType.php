<?php

namespace Darkwood\HearthbreakerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SourceType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('title', 'text', array('required' => false))
		;
	}

	public function getName()
	{
		return 'source';
	}
}
