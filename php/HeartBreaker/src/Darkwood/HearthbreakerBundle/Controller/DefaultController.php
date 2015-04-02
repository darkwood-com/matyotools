<?php

namespace Darkwood\HearthbreakerBundle\Controller;

use Darkwood\HearthbreakerBundle\Entity\UserCard;
use Darkwood\HearthbreakerBundle\Services\UserCardService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
	public function cardAction(Request $request)
	{
        $user = $this->getUser();
        if(!$user) {
            throw new AccessDeniedHttpException();
        }

		$form = $this->createFormBuilder()
			->add('title', 'text', array('required' => false))
			->add('type', 'choice', array(
				'choices'   => array(
					'Arme' => 'Arme',
					'Serviteur' => 'Serviteur',
					'Sort' => 'Sort',
				),
				'required' => false
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
				'required' => false
			))
			->add('rarity', 'choice', array(
				'choices'   => array(
					'Basique' => 'Basique',
					'Commune' => 'Commune',
					'Rare' => 'Rare',
					'Epique' => 'Epique',
					'Légendaire' => 'Légendaire',
				),
				'required' => false
			))
			->add('cost', 'integer', array('required' => false))
			->add('attack', 'integer', array('required' => false))
			->add('health', 'integer', array('required' => false))
			->add('submit', 'submit')
			->getForm()
		;

		$search = array();
		$form->handleRequest($request);
		if($form->isValid()) {
			$search = $form->getData();
		}

		$cards = $this->get('hb.card')->search($search);

        $cardsQuantity = array();
        $userCards = $this->get('hb.userCard')->findByUser($user);
        foreach($userCards as $userCard) {
            /** @var UserCard $userCard */
            $id = $userCard->getCard()->getId();
            $isGolden = $userCard->getIsGolden() ? '1' : '0';
            $cardsQuantity[$id][$isGolden] = $userCard->getQuantity();
        }

		return $this->render('HearthbreakerBundle:Default:card.html.twig', array(
			'nav' => 'card',
			'cards' => $cards,
			'form' => $form->createView(),
            'cardsQuantity' => $cardsQuantity,
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

	public function deckAction(Request $request)
	{
		$user = $this->getUser();
		if(!$user) {
			throw new AccessDeniedHttpException();
		}

		$form = $this->createFormBuilder()
			->add('title', 'text', array('required' => false))
			->add('submit', 'submit')
			->getForm()
		;

		$search = array();
		$form->handleRequest($request);
		if($form->isValid()) {
			$search = $form->getData();
		}

		$decks = $this->get('hb.deck')->search($search);

		return $this->render('HearthbreakerBundle:Default:deck.html.twig', array(
			'nav' => 'deck',
			'decks' => $decks,
			'form' => $form->createView(),
		));
	}

	public function deckDetailAction($slug)
	{
		$user = $this->getUser();
		if(!$user) {
			throw new AccessDeniedHttpException();
		}

		$deck = $this->get('hb.deck')->findBySlug($slug);

		if(!$deck) {
			throw new NotFoundHttpException();
		}

		$cardsQuantity = array();
		$userCards = $this->get('hb.userCard')->findByUserAndDeck($user, $deck);
		foreach($userCards as $userCard) {
			/** @var UserCard $userCard */
			$id = $userCard->getCard()->getId();

			if(!isset($cardsQuantity[$id])) {
				$cardsQuantity[$id] = array('0' => 0, '1' => 0, 'total' => 0);
			}

			$isGolden = $userCard->getIsGolden() ? '1' : '0';
			$cardsQuantity[$id][$isGolden] = $userCard->getQuantity();
			$cardsQuantity[$id]['total'] += $userCard->getQuantity();
		}

		return $this->render('HearthbreakerBundle:Default:deckDetail.html.twig', array(
			'nav' => 'deck',
			'deck' => $deck,
			'cardsQuantity' => $cardsQuantity,
		));
	}

	public function userCardAction($slug, $isGolden)
	{
        $user = $this->getUser();
        if(!$user) {
            throw new AccessDeniedHttpException();
        }

        $card = $this->get('hb.card')->findBySlug($slug);

        if(!$card) {
            throw new NotFoundHttpException();
        }

        $isGolden = boolval($isGolden);

        /** @var UserCardService $userCardService */
        $userCardService = $this->get('hb.userCard');
		$userCard = $userCardService->findByUserAndCard($user, $card, $isGolden);
		if(!$userCard) {
            $userCard = new UserCard();
            $userCard->setUser($user);
            $userCard->setCard($card);
            $userCard->setIsGolden($isGolden);
        }
        $quantity = ($userCard->getQuantity() + 1) % 3;
        $userCard->setQuantity($quantity);
        $userCardService->save($userCard);

		return new JsonResponse($quantity);
	}
}
