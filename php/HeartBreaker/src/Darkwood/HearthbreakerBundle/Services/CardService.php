<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Darkwood\HearthpwnBundle\Entity\CardHearthpwn;
use Darkwood\HearthstatsBundle\Entity\CardHearthstats;
use Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks;
use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\CardRepository;
use Symfony\Component\DependencyInjection\ContainerAware;

class CardService extends ContainerAware
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
     * @var CardRepository
     */
    private $cardRepository;

    /**
     * @param EntityManager $em
     * @param CacheService  $cacheService
     */
    public function __construct(EntityManager $em, CacheService $cacheService)
    {
        $this->em = $em;
		$this->cardRepository = $em->getRepository('HearthbreakerBundle:Card');
        $this->cacheService = $cacheService;
    }

    /**
     * Save a card.
     *
     * @param Card $card
     */
    public function save(Card $card)
    {
        $this->em->persist($card);
        $this->em->flush();
    }

    /**
     * Remove one card.
     *
     * @param Card $card
     */
    public function remove(Card $card)
    {
        $this->em->remove($card);
        $this->em->flush();
    }

    public function findAll()
    {
        return $this->cardRepository->findAll();
    }

    /**
     * @param $slug
     * @param null $source
     *
     * @return null|Card
     */
    public function findBySlug($slug, $source = null)
    {
        return $this->cardRepository->findBySlug($slug, $source);
    }

    public function count($source = null)
    {
        return $this->cardRepository->count($source);
    }

    public function search($search)
    {
        return $this->cardRepository->search($search);
    }

    /**
     * @param Card $card
     *
     * @return string
     */
    public function getUrl($card)
    {
        /** @var \Symfony\Component\Routing\Router $router */
        $router = $this->container->get(sprintf('hb.%s.router', $card->getSource()));

        return $router->generate('card_detail', array('slug' => $card->getSlug()), true);
    }

	/**
	 * @param Card $card
	 * @param null|string|array $sources
	 * @return array
	 */
	public function getSiblings($card, $sources = null)
	{
		$key = implode('-', array('card-siblings', $card->getSource(), $card->getSlug(), $sources));

		return $this->cacheService->fetch($key, function () use ($card, $sources) {
			$cards = $this->cardRepository->siblings($card);
			if(!is_null($sources)) {
				if(!is_array($sources)) $sources = array($sources);
				$cards = array_filter($cards, function($c) use ($sources) {
					/** @var Card $c */
					return in_array($c->getSource(), $sources);
				});
			}
			return $cards;
		}, 'card');
	}

    /**
     * @param Card $iCard
     * @param Card $jCard
     *
     * @return int
     */
    public function compare($iCard, $jCard)
    {
        $names = array_map(function ($card) {
            if ($card instanceof CardHearthstonedecks) {
                return $card->getNameEn();
            } elseif ($card instanceof CardHearthstats || $card instanceof CardHearthpwn) {
                return $card->getName();
            }

            return $card->getName();
        }, array($iCard, $jCard));

        return levenshtein($names[0], $names[1]);
    }

    public function identify()
    {
        $cards = $this->findAll();
        foreach ($cards as $card) {
            /* @var Card $card */
            $card->setIdentifier(null);
        }

        $identifier = 1;

        $leftCards = array_values($cards);
        for ($lvl = 0; $lvl < 2; $lvl++) {
            foreach ($leftCards as $i => $iCard) {
                /** @var Card $iCard */
                if ($iCard->getIdentifier()) {
                    unset($leftCards[$i]);
                    continue;
                }

                $mCards = array();
                foreach ($cards as $jCard) {
                    if ($iCard === $jCard) {
                        continue;
                    }

                    $cmp = $this->compare($iCard, $jCard);
                    if ($cmp != -1 && $cmp < $lvl) {
                        $mCards[] = $jCard;
                    }
                }

                if (count($mCards) > 0) {
                    $mIdentifier = null;
                    foreach ($mCards as $mCard) {
                        /* @var Card $mCard */
                        $cIdentifier = $mCard->getIdentifier();
                        if (!is_null($cIdentifier)) {
                            if ($cIdentifier != $mIdentifier) {
                                unset($leftCards[$i]);
                                continue;
                            }

                            $mIdentifier = $cIdentifier;
                        }
                    }

                    $cIdentifier = $identifier;
                    if (!is_null($mIdentifier)) {
                        $cIdentifier = $mIdentifier;
                    } else {
                        $identifier ++;
                    }

                    $iCard->setIdentifier($cIdentifier);
                    foreach ($mCards as $mCard) {
                        $mCard->setIdentifier($cIdentifier);
                    }

                    unset($leftCards[$i]);
                }
            }
        }

        $this->em->flush();
    }

    /**
     * @param Card $card
     * @param bool $golden
     *
     * @return int
     */
    public function getBuy($card, $golden = false)
    {
        $key = implode('-', array('card-buy', $card->getSource(), $card->getSlug(), $golden));

        return $this->cacheService->fetch($key, function () use ($card, $golden) {
            switch ($card->getRarity()) {
                case 'Légendaire':
                    return $golden ? 3200 : 1600;
                    break;
                case 'Epique':
                    return $golden ? 1600 : 400;
                    break;
                case 'Rare':
                    return $golden ? 800 : 100;
                    break;
                case 'Commune':
                    return $golden ? 400 : 40;
                    break;
            }

            return 0;
        }, 'card');
    }

    /**
     * @param Card $card
     * @param bool $golden
     *
     * @return int
     */
    public function getSell($card, $golden = false)
    {
        $key = implode('-', array('card-sell', $card->getSource(), $card->getSlug(), $golden));

        return $this->cacheService->fetch($key, function () use ($card, $golden) {
            switch ($card->getRarity()) {
                case 'Légendaire':
                    return $golden ? 1600 : 400;
                    break;
                case 'Epique':
                    return $golden ? 400 : 100;
                    break;
                case 'Rare':
                    return $golden ? 100 : 20;
                    break;
                case 'Commune':
                    return $golden ? 50 : 5;
                    break;
            }

            return 0;
        }, 'card');
    }
}
