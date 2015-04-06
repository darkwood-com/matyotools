<?php

namespace Darkwood\HearthbreakerBundle\Controller;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Darkwood\HearthbreakerBundle\Entity\Deck;
use Darkwood\HearthbreakerBundle\Entity\DeckCard;
use Darkwood\HearthbreakerBundle\Entity\UserCard;
use Darkwood\HearthbreakerBundle\Services\CardService;
use Darkwood\HearthbreakerBundle\Services\DeckService;
use Darkwood\HearthbreakerBundle\Services\UserCardService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    private function cardQuantity($user, $deck = null)
    {
        $cardsQuantity = array();
        $userCards = $this->get('hb.userCard')->findByUserAndDeck($user, $deck);
        foreach ($userCards as $userCard) {
            /* @var UserCard $userCard */
            $id = $userCard->getCard()->getId();

            if (!isset($cardsQuantity[$id])) {
                $cardsQuantity[$id] = array('0' => 0, '1' => 0, 'total' => 0);
            }

            $isGolden = $userCard->getIsGolden() ? '1' : '0';
            $cardsQuantity[$id][$isGolden] = $userCard->getQuantity();
            $cardsQuantity[$id]['total'] += $userCard->getQuantity();
        }

        return $cardsQuantity;
    }

    public function cardAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createFormBuilder()
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
            ->getForm()
        ;

        $search = array();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $search = $form->getData();
        }

        $cards = $this->get('hb.card')->search($search);

        $cardsQuantity = $this->cardQuantity($user);

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

        return $this->render('HearthbreakerBundle:Default:cardDetail.html.twig', array(
            'nav' => 'card',
            'card' => $card,
            'url' => $url,
        ));
    }

    private function percent($percent)
    {
        if ($percent['total'] > 0) {
            return number_format($percent['value'] / $percent['total'] * 100, 1);
        }

        return 0;
    }

    public function deckAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createFormBuilder()
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
            ->getForm()
        ;

        $search = array();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $search = $form->getData();
        }

        $decks = $this->get('hb.deck')->search($search);

        $cardsQuantity = $this->cardQuantity($user);

        $decks = array_map(function ($deck) use ($cardsQuantity) {
            /* @var Deck $deck */

            $cardPercent = array('value' => 0, 'total' => 0);
            $buyPercent = array('value' => 0, 'total' => $deck->getBuy());

            $deckCards = $deck->getCards();
            foreach ($deckCards as $deckCard) {
                /* @var DeckCard $deckCard */
                $card = $deckCard->getCard();
                $cardId = $card->getId();

                if (isset($cardsQuantity[$cardId])) {
                    $userQuantity = min($cardsQuantity[$cardId]['total'], $deckCard->getQuantity());

                    $cardPercent['value'] += $userQuantity;
                    $buyPercent['value'] += $userQuantity * $card->getBuy();
                }

                $cardPercent['total'] += $deckCard->getQuantity();
            }

            return array(
                'cardPercent' => $this->percent($cardPercent),
                'buyPercent' => $this->percent($buyPercent),
                'deck' => $deck,
            );
        }, $decks);

        $decks = array_filter($decks, function ($deck) use ($search) {
            if ((isset($search['class']) && $search['class'] != null && $deck['deck']->getClass() != $search['class'])
            || (isset($search['buy']) && $search['buy'] != null && $deck['deck']->getBuy() < $search['buy'])
            || (isset($search['card_percent']) && $search['card_percent'] != null && $deck['cardPercent'] < $search['card_percent'])
            || (isset($search['buy_percent']) && $search['buy_percent'] != null && $deck['buyPercent'] < $search['buy_percent'])) {
                return false;
            }

            return true;
        });

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

        $cardsQuantity = $this->cardQuantity($user, $deck);

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

    public function userCardAction($source, $slug, $isGolden)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException();
        }

        $card = $this->get('hb.card')->findBySlug($slug, $source);

        if (!$card) {
            throw new NotFoundHttpException();
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
