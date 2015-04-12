<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\Deck;
use Darkwood\HearthbreakerBundle\Entity\DeckCard;
use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\DeckRepository;
use Symfony\Component\DependencyInjection\ContainerAware;

class DeckService extends ContainerAware
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var DeckRepository
     */
    private $deckRepository;

    /**
     * @var CardService
     */
    private $cardService;

    /**
     * @var UserCardService
     */
    private $userCardService;

    /**
     * @param EntityManager $em
     * @param CardService $cardService
     * @param UserCardService $userCardService
     */
    public function __construct(EntityManager $em, CardService $cardService, UserCardService $userCardService)
    {
        $this->em = $em;
        $this->deckRepository = $em->getRepository('HearthbreakerBundle:Deck');
        $this->cardService = $cardService;
        $this->userCardService = $userCardService;
    }

    /**
     * Save a deck.
     *
     * @param Deck $deck
     */
    public function save(Deck $deck)
    {
        $this->em->persist($deck);
        $this->em->flush();
    }

    /**
     * Remove one deck.
     *
     * @param Deck $deck
     */
    public function remove(Deck $deck)
    {
        $this->em->remove($deck);
        $this->em->flush();
    }

    public function findAll()
    {
        return $this->deckRepository->findAll();
    }

    /**
     * @param $slug
     * @param null $source
     *
     * @return null|Deck
     */
    public function findBySlug($slug, $source = null)
    {
        return $this->deckRepository->findBySlug($slug, $source);
    }

    private function percent($percent)
    {
        if ($percent['total'] > 0) {
            return number_format($percent['value'] / $percent['total'] * 100, 1);
        }

        return 0;
    }

    public function search($search, $user = null)
    {
        $decks = $this->deckRepository->search($search);

        $cardsQuantity = $user ? $this->userCardService->cardQuantity($user) : array();

        $decks = array_map(function ($deck) use ($cardsQuantity) {
            /* @var Deck $deck */

            $cardPercent = array('value' => 0, 'total' => 0);
            $buyPercent = array('value' => 0, 'total' => $this->getBuy($deck));

            $deckCards = $deck->getCards();
            foreach ($deckCards as $deckCard) {
                /* @var DeckCard $deckCard */
                $card = $deckCard->getCard();
                $cardId = $card->getId();

                if (isset($cardsQuantity[$cardId])) {
                    $userQuantity = min($cardsQuantity[$cardId]['total'], $deckCard->getQuantity());

                    $cardPercent['value'] += $userQuantity;
                    $buyPercent['value'] += $userQuantity * $this->cardService->getBuy($card);
                }

                $cardPercent['total'] += $deckCard->getQuantity();
            }

            return array(
                'cardPercent' => $this->percent($cardPercent),
                'buyPercent' => $this->percent($buyPercent),
                'buy' => $this->getBuy($deck),
                'deck' => $deck,
            );
        }, $decks);

        return array_filter($decks, function ($deck) use ($search) {
            if ((isset($search['class']) && $search['class'] != null && $this->getClass($deck['deck']) != $search['class'])
                || (isset($search['buy']) && $search['buy'] != null && $this->getBuy($deck['deck']) < $search['buy'])
                || (isset($search['card_percent']) && $search['card_percent'] != null && $deck['cardPercent'] < $search['card_percent'])
                || (isset($search['buy_percent']) && $search['buy_percent'] != null && $deck['buyPercent'] < $search['buy_percent'])) {
                return false;
            }

            return true;
        });
    }

    /**
     * @param Deck $deck
     * @return string
     */
    public function getUrl($deck)
    {
        /** @var \Symfony\Component\Routing\Router $router */
        $router = $this->container->get(sprintf('hb.%s.router', $deck->getSource()));
        return $router->generate('deck_detail', array('slug' => $deck->getSlug()), true);
    }

    /**
     * @param Deck $deck
     * @return int
     */
    public function getBuy($deck)
    {
        $cristal = 0;

        $cards = $deck->getCards();
        foreach ($cards as $deckCard) {
            /* @var DeckCard $deckCard */
            $cristal += $this->cardService->getBuy($deckCard->getCard()) * $deckCard->getQuantity();
        }

        return $cristal;
    }

    /**
     * @param Deck $deck
     * @return int
     */
    public function getSell($deck)
    {
        $cristal = 0;

        $cards = $deck->getCards();
        foreach ($cards as $deckCard) {
            /* @var DeckCard $deckCard */
            $cristal += $this->cardService->getSell($deckCard->getCard()) * $deckCard->getQuantity();
        }

        return $cristal;
    }

    /**
     * @param Deck $deck
     * @return string
     */
    public function getClass($deck)
    {
        $classes = array_map(function ($deckCard) {
            /* @var DeckCard $deckCard */
            return $deckCard->getCard()->getPlayerClass();
        }, $deck->getCards()->toArray());
        $classes = array_filter($classes, function ($class) {
            return $class != 'Neutre';
        });
        $classes = array_unique($classes);

        return current($classes);
    }
}
