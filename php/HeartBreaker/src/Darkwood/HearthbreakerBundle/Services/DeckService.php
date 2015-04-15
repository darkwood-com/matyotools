<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\Deck;
use Darkwood\HearthbreakerBundle\Entity\DeckCard;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @var CacheService
     */
    private $cacheService;

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
     * @param EntityManager   $em
     * @param CacheService    $cacheService
     * @param CardService     $cardService
     * @param UserCardService $userCardService
     */
    public function __construct(EntityManager $em, CacheService $cacheService, CardService $cardService, UserCardService $userCardService)
    {
        $this->em = $em;
		$this->deckRepository = $em->getRepository('HearthbreakerBundle:Deck');
        $this->cacheService = $cacheService;
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

            $deckCards = $this->getCards($deck);
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
     *
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
	 * @param bool $siblings
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCards($deck, $siblings = false)
	{
		$key = implode('-', array('deck-cards', $deck->getSource(), $deck->getSlug(), $siblings));

		return $this->cacheService->fetch($key, function () use ($deck, $siblings) {
			$cards = $deck->getCards();

			if($siblings) {
				return new ArrayCollection(array_reduce($cards, function($carry, $deckCard) use ($siblings) {
					/** @var DeckCard $deckCard */
					$cardSiblings = $this->cardService->getSiblings($deckCard->getCard(), $siblings);
					foreach($cardSiblings as $cardSibling)
					{
						$deckCardSibling = new DeckCard();
						$deckCardSibling->setDeck($deckCard->getDeck());
						$deckCardSibling->setCard($cardSibling);
						$deckCardSibling->setQuantity($deckCard->getQuantity());

						$carry[] = $deckCardSibling;
					}

					return $carry;
				}, array()));
			}

			return $cards;
		}, 'deck');
	}

    /**
     * @param Deck $deck
     *
     * @return int
     */
    public function getBuy($deck)
    {
        $key = implode('-', array('deck-buy', $deck->getSource(), $deck->getSlug()));

        return $this->cacheService->fetch($key, function () use ($deck) {
            $cristal = 0;

            $cards = $this->getCards($deck, 'hearthstonedecks');
            foreach ($cards as $deckCard) {
                /* @var DeckCard $deckCard */
                $cristal += $this->cardService->getBuy($deckCard->getCard()) * $deckCard->getQuantity();
            }

            return $cristal;
        }, 'deck');
    }

    /**
     * @param Deck $deck
     *
     * @return int
     */
    public function getSell($deck)
    {
        $key = implode('-', array('deck-sell', $deck->getSource(), $deck->getSlug()));

        return $this->cacheService->fetch($key, function () use ($deck) {
            $cristal = 0;

            $cards = $this->getCards($deck, 'hearthstonedecks');
            foreach ($cards as $deckCard) {
                /* @var DeckCard $deckCard */
                $cristal += $this->cardService->getSell($deckCard->getCard()) * $deckCard->getQuantity();
            }

            return $cristal;
        }, 'deck');
    }

    /**
     * @param Deck $deck
     *
     * @return string
     */
    public function getClass($deck)
    {
        $key = implode('-', array('deck-class', $deck->getSource(), $deck->getSlug()));

        return $this->cacheService->fetch($key, function () use ($deck) {
            $classes = array_map(function ($deckCard) {
                /* @var DeckCard $deckCard */
                return $deckCard->getCard()->getPlayerClass();
            }, $this->getCards($deck, 'hearthstonedecks')->toArray());
            $classes = array_filter($classes, function ($class) {
                return $class != 'Neutre';
            });
            $classes = array_unique($classes);

            return current($classes);
        }, 'deck');
    }
}
