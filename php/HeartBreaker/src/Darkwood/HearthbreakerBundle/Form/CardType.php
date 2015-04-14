<?php

namespace Darkwood\HearthbreakerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CardType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('source', 'choice', array(
				'choices'   => array(
					'hearthstonedecks' => 'Hearthstone Decks',
					'hearthpwn' => 'Hearthpwn',
				),
				'required' => false,
			))
			->add('title', 'text', array('required' => false))
			->add('type', 'choice', array(
				'choices'   => array(
					'Arme' => 'Arme',
					'Serviteur' => 'Serviteur',
					'Sort' => 'Sort',
				),
				'required' => false,
			))
			->add('class', 'choice', array(
				'choices'   => array(
					'Chaman' => 'Chaman',
					'Chasseur' => 'Chasseur',
					'Démoniste' => 'Démoniste',
					'Druide' => 'Druide',
					'Guerrier' => 'Guerrier',
					'Mage' => 'Mage',
					'Neutre' => 'Neutre',
					'Paladin' => 'Paladin',
					'Prêtre' => 'Prêtre',
					'Voleur' => 'Voleur',
				),
				'required' => false,
			))
			->add('rarity', 'choice', array(
				'choices'   => array(
					'Basique' => 'Basique',
					'Commune' => 'Commune',
					'Rare' => 'Rare',
					'Epique' => 'Epique',
					'Légendaire' => 'Légendaire',
				),
				'required' => false,
			))
			->add('cost', 'integer', array('required' => false))
			->add('attack', 'integer', array('required' => false))
			->add('health', 'integer', array('required' => false))
		;
	}

	public function getName()
	{
		return 'card';
	}
}
