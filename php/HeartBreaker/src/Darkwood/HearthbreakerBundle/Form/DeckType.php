<?php

namespace Darkwood\HearthbreakerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DeckType extends AbstractType
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
			->add('class', 'choice', array(
				'choices'   => array(
					'Chaman' => 'Chaman',
					'Chasseur' => 'Chasseur',
					'Démoniste' => 'Démoniste',
					'Druide' => 'Druide',
					'Guerrier' => 'Guerrier',
					'Mage' => 'Mage',
					'Paladin' => 'Paladin',
					'Prêtre' => 'Prêtre',
					'Voleur' => 'Voleur',
				),
				'required' => false,
			))
			->add('buy', 'integer', array('required' => false))
			->add('card_percent', 'integer', array('required' => false))
			->add('buy_percent', 'integer', array('required' => false))
			->add('vote_up', 'integer', array('required' => false))
			->add('vote_down', 'integer', array('required' => false))
			->add('rating', 'integer', array('required' => false))
			->add('since', 'integer', array('required' => false))
		;
	}

	public function getName()
	{
		return 'deck';
	}
}
