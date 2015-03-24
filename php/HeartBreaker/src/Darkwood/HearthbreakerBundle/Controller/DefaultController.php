<?php

namespace Darkwood\HearthbreakerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	public function cardAction()
	{
		$cards = $this->get('hb.card')->findAll();

		return $this->render('HearthbreakerBundle:Default:card.html.twig', array(
			'nav' => 'card',
			'cards' => $cards,
		));
	}

	public function deckAction()
	{
		$decks = $this->get('hb.deck')->findAll();

		return $this->render('HearthbreakerBundle:Default:deck.html.twig', array(
			'nav' => 'deck',
			'decks' => $decks,
		));
	}
}
