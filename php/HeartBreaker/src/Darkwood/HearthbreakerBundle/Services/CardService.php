<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Darkwood\HearthpwnBundle\Entity\CardHearthpwn;
use Darkwood\HearthstatsBundle\Entity\CardHearthstats;
use Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks;
use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\CardRepository;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\DependencyInjection\ContainerAware;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

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
	 * @param string $name
	 * @param string $lang
	 * @param array $toLangs
	 * @return array
	 */
	private function findNames($name, $lang, $toLangs)
	{
		$key = implode('-', array('card-names', $name, $lang, implode(':', $toLangs)));

		return $this->cacheService->fetch($key, function () use ($name, $lang, $toLangs) {
			$key = implode('-', array('card-hearthstonejson-lang', $lang, implode(':', $toLangs)));

			$langs = $this->cacheService->fetch($key, function () use ($lang, $toLangs) {
				$key = implode('-', array('card-hearthstonejson-names'));

				$cards = $this->cacheService->fetch($key, function () {
					$client = new GuzzleClient(array('defaults' => array('allow_redirects' => false, 'cookies' => true)));
					$response = $client->get('http://hearthstonejson.com/json/AllSetsAllLanguages.json');
					$json = $response->getBody();
					$jsonDecode = new JsonDecode(true);
					$json = $jsonDecode->decode($json, JsonEncoder::FORMAT);

					$cardNames = array();
					foreach($json as $lang => $cardType) {
						foreach($cardType as $cards) {
							foreach($cards as $card) {
								$cardNames[$card['id']][$lang] = $card['name'];
							}
						}
					}

					return $cardNames;
				}, 'hearthstonejson');

				$langs = array();
				foreach($cards as $card) {
					$name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $card[$lang]));

					foreach($toLangs as $toLang) {
						$langs[$name][] = $card[$toLang];
					}
				}

				return array_map(function($l) {
					return array_unique($l);
				}, $langs);
			}, 'hearthstonejson');

			$name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));

			return isset($langs[$name]) ? $langs[$name] : array();
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
			/** @var Card $card */
			$key = implode('-', array('card-compare-names', $card->getSource(), $card->getSlug()));

			return $this->cacheService->fetch($key, function () use ($card) {
				$names = array($card->getName());

				if ($card instanceof CardHearthstonedecks) {
					return $names = array_merge(array($card->getNameEn()), $this->findNames($card->getName(), 'frFR', array('enUS', 'enGB')));
				} elseif ($card instanceof CardHearthstats || $card instanceof CardHearthpwn) {
					return $names = array($card->getName());
				}

				$names = array_merge($names, array_map(function ($name) {
					return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
				}, $names));
				$names = array_unique($names);

				return $names;
			}, 'card');
		}, array($iCard, $jCard));

		$levs = array();
		foreach ($names[0] as $iName) {
			foreach ($names[1] as $jName) {
				$levs[] = levenshtein($iName, $jName);
			}
		}

		return min($levs);
    }

    public function identify(ProgressBar $progressBar)
    {
        /** @var Card[] $cards */
        $cards = $this->findAll();

        $progressBar->start(count($cards));

        $id = 1;

        /** @var Card $iCard */
        $iCard = array_shift($cards);
        while($iCard)
        {
            $iCard->setIdentifier(null);

            $keys = array();
            foreach($cards as $key => $jCard)
            {
                $lev = $this->compare($iCard, $jCard);
                if($lev != -1 && $lev <= 1) {
                    $keys[] = $key;
                }
            }

            if(count($keys) > 0) {
                $iCard->setIdentifier($id);
                foreach($keys as $key) {
                    $cards[$key]->setIdentifier($id);
                    unset($cards[$key]);
                    $progressBar->advance();
                }
                $id ++;
            }

            $iCard = array_shift($cards);
            $progressBar->advance();
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
