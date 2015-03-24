<?php

namespace Darkwood\HearthbreakerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

	public function cardDetailAction($slug)
	{
		$card = $this->get('hb.card')->findBySlug($slug);

		if(!$card) {
			throw new NotFoundHttpException();
		}

		return $this->render('HearthbreakerBundle:Default:cardDetail.html.twig', array(
			'nav' => 'card',
			'card' => $card,
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

	public function deckDetailAction($slug)
	{
		$deck = $this->get('hb.deck')->findBySlug($slug);

		if(!$deck) {
			throw new NotFoundHttpException();
		}

		return $this->render('HearthbreakerBundle:Default:deckDetail.html.twig', array(
			'nav' => 'deck',
			'deck' => $deck,
		));
	}
}
