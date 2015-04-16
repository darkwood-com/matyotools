<?php

namespace Darkwood\HearthbreakerBundle\Controller;


use Darkwood\HearthbreakerBundle\Form\CardType;
use Darkwood\HearthbreakerBundle\Entity\Card;
use Darkwood\HearthbreakerBundle\Entity\Deck;
use Darkwood\HearthbreakerBundle\Entity\DeckCard;
use Darkwood\HearthbreakerBundle\Entity\UserCard;
use Darkwood\HearthbreakerBundle\Form\DeckType;
use Darkwood\HearthbreakerBundle\Form\SourceType;
use Darkwood\HearthbreakerBundle\Services\CardService;
use Darkwood\HearthbreakerBundle\Services\DeckService;
use Darkwood\HearthbreakerBundle\Services\UserCardService;
use Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function cardAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(new CardType());

        $search = array();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $search = $form->getData();
        }

        $cards = $this->get('hb.card')->search($search);
        $cardsQuantity = $this->get('hb.userCard')->cardQuantity($user);

        return $this->render('HearthbreakerBundle:Default:card.html.twig', array(
            'nav' => 'card',
            'cards' => $cards,
            'form' => $form->createView(),
            'cardsQuantity' => $cardsQuantity,
        ));
    }

    public function cardDetailAction($source, $slug)
    {
        /** @var CardService $cardService */
        $cardService = $this->get('hb.card');
        $card = $cardService->findBySlug($slug, $source);

        if (!$card) {
            throw new NotFoundHttpException();
        }

        $url = $cardService->getUrl($card);
        $buy = $cardService->getBuy($card);
        $sell = $cardService->getSell($card);

        return $this->render('HearthbreakerBundle:Default:cardDetail.html.twig', array(
            'nav' => 'card',
            'card' => $card,
            'url' => $url,
            'buy' => $buy,
            'sell' => $sell,
        ));
    }

    public function deckAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(new DeckType());

        $search = array();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $search = $form->getData();
        }

        /* @var DeckService $deckService */
        $decks = $this->get('hb.deck')->search($search, $user);

        return $this->render('HearthbreakerBundle:Default:deck.html.twig', array(
            'nav' => 'deck',
            'decks' => $decks,
            'form' => $form->createView(),
        ));
    }

    public function deckDetailAction($source, $slug)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException();
        }

        /** @var DeckService $deckService */
        $deckService = $this->get('hb.deck');
        $deck = $deckService->findBySlug($slug, $source);

        if (!$deck) {
            throw new NotFoundHttpException();
        }

        $url = $deckService->getUrl($deck);

        $cardsQuantity = $this->get('hb.userCard')->cardQuantity($user, $deck);

        $cards = $deck->getCards();
        $cardsByClass = array();
        foreach ($cards as $deckCard) {
            /* @var DeckCard $deckCard */
            $class = $deckCard->getCard()->getPlayerClass();
            $cardsByClass[$class][] = $deckCard;
        }
        $cardsByClass = array_map(function ($deckCards) {
            return array(
                'cards' => $deckCards,
                'count' => array_reduce($deckCards, function ($carry, $deckCard) {
                    /* @var DeckCard $deckCard */
                    return $carry + $deckCard->getQuantity();
                }, 0),
            );
        }, $cardsByClass);
        uksort($cardsByClass, function ($c1, $c2) {
            if ($c1 == $c2) {
                return 0;
            }
            if ($c1 == 'Neutre') {
                return 1;
            }
            if ($c2 == 'Neutre') {
                return -1;
            }

            return 0;
        });

        return $this->render('HearthbreakerBundle:Default:deckDetail.html.twig', array(
            'nav' => 'deck',
            'deck' => $deck,
            'url' => $url,
            'cardsByClass' => $cardsByClass,
            'cardsQuantity' => $cardsQuantity,
        ));
    }

	public function sourceAction(Request $request)
	{
		$user = $this->getUser();
		if (!$user) {
			throw new AccessDeniedHttpException();
		}

		$form = $this->createForm(new SourceType());

		$search = array();
		$form->handleRequest($request);
		if ($form->isValid()) {
			$search = $form->getData();
		}

		/** @var Card[] $cards */
		$cards = $this->get('hb.card')->search($search);
		$cardsQuantity = $this->get('hb.userCard')->cardQuantity($user);
		$cardsByIdentifier = array();
		foreach($cards as $card) {
			$cardsByIdentifier[$card->getIdentifier()][] = $card;
		}

		if(isset($search['missing']) && $search['missing'] === true) {
			foreach($cardsByIdentifier as $identifier => &$cards)
			{
				$userCards = array_filter($cards, function($card) use ($cardsQuantity) {
					$cardId = $card->getId();
					return isset($cardsQuantity[$cardId]) && $cardsQuantity[$cardId]['total'] > 0;
				});

				$sources = array_unique(array_map(function($card) {
					return $card->getSource();
				}, $cards));

				if(!is_null($identifier) && count($sources) == 3) {
					$cards = null;
				} else {
					$cards = $userCards;
				}
			}
			$cardsByIdentifier = array_filter($cardsByIdentifier);
		}

		ksort($cardsByIdentifier);

		return $this->render('HearthbreakerBundle:Default:source.html.twig', array(
			'nav' => 'source',
			'cards' => $cardsByIdentifier,
			'form' => $form->createView(),
			'cardsQuantity' => $cardsQuantity,
		));
	}

    public function userCardAction($source, $slug, $isGolden)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException();
        }

		/** @var Card $card */
        $card = $this->get('hb.card')->findBySlug($slug, $source);

        if (!$card) {
            throw new NotFoundHttpException();
        } else if (!$card instanceof CardHearthstonedecks) {
			throw new AccessDeniedException();
		}

        $isGolden = boolval($isGolden);

        /** @var UserCardService $userCardService */
        $userCardService = $this->get('hb.userCard');
        $userCard = $userCardService->findByUserAndCard($user, $card, $isGolden);
        if (!$userCard) {
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
