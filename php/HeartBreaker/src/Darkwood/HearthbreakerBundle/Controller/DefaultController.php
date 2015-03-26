<?php

namespace Darkwood\HearthbreakerBundle\Controller;

use Darkwood\HearthbreakerBundle\Entity\UserCard;
use Darkwood\HearthbreakerBundle\Services\UserCardService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
	public function cardAction()
	{
        $user = $this->getUser();
        if(!$user) {
            throw new AccessDeniedHttpException();
        }

		$cards = $this->get('hb.card')->findAll();

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
