<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Doctrine\Common\Cache\Cache;
use Goutte\Client;
use GuzzleHttp\Subscriber\Cache\CacheStorage;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Router;

class ScrapperService
{
	/**
	 * @var Cache
	 */
    private $cache;

	/**
	 * @var Router
	 */
	private $router;

	/**
	 * @var CardService
	 */
	private $cardService;

	/**
	 * @var DeckService
	 */
	private $deckService;

    public function __construct(Cache $cache, Router $router, CardService $cardService, DeckService $deckService)
    {
        $this->cache = $cache;
		$this->router = $router;
		$this->cardService = $cardService;
		$this->deckService = $deckService;
    }

	/**
	 * @param $name
	 * @param array $parameters
	 * @return Crawler
	 */
    private function request($name, $parameters = array())
    {
		$url = $this->router->generate($name, $parameters, true);

        static $client = null;

        if(!$client) {
            $client = new Client();

            $guzzle = $client->getClient();
            $guzzle->setDefaultOption('debug', true);
            CacheSubscriber::attach($guzzle, array(
                'storage' => new CacheStorage($this->cache),
                'validate' => false,
                'can_cache' => function () {
                    return true;
                }
            ));

            $guzzle->getEmitter()->on(
                'complete',
                function (\GuzzleHttp\Event\CompleteEvent $event) {
                    $response = $event->getResponse();
                    $response->setHeader('Cache-Control', 'max-age=86400'); //1 day
                },
                'first'
            );
        }

        return $client->request('GET', $url);
    }

    public function syncCardList($force = false)
    {
		$page = 1;
		do {
			$crawler = $this->request('card_list', array('page' => $page));

			$crawler
				->filter('#liste_cartes .carte_galerie_container > a')
				->each(function(Crawler $node) use(&$slugs) {
					try {
						$href = $node->attr('href');
						$match = $this->router->match($href);
						if($match['_route'] == 'card_detail') {
							$this->syncCard($match['slug']);
						}
					} catch (ResourceNotFoundException $e) {
					} catch (MethodNotAllowedException $e) {
					}
				});

			$cardsNumber = intval($crawler->filter('#liste_cartes strong')->first()->text());
			$hasNext = $crawler->filter('#liste_cartes .pagination')->children()
				->reduce(function(Crawler $node) {
					return $node->text() == 'Suiv';
				})->count() > 0;

			echo $crawler->filter('#liste_cartes .pagination')->children()
				->reduce(function(Crawler $node) {
					return $node->text() == 'Suiv';
				})->count()."**\n";

			$page += 1;

		} while($this->cardService->count() < $cardsNumber && $hasNext);
    }

	public function syncCard($slug, $force = false)
	{
		$card = $this->cardService->findBySlug($slug);
		if(!$card) {
			$card = new Card();
			$card->setSlug($slug);
		} elseif (!$force) {
			return $card;
		}

		$crawler = $this->request('card_detail', array('slug' => $slug));

		$attr = null;
		$crawler
			->filter('#informations-cartes td')
			->each(function(Crawler $node, $i) use ($card, &$attr) {
				$text = $node->text();
				if($i % 2 == 0) {
					$attr = $text;
				} else {
					switch($attr) {
						case "Nom": $card->setName($text); break;
						case "Coût en mana": $card->setCost(intval($text)); break;
						case "Attaque": $card->setAttack(intval($text)); break;
						case "Vie": $card->setHealth(intval($text)); break;
						case "Race": $card->setRace($text); break;
						case "Description": $card->setText($text); break;
						case "Texte d'ambiance": $card->setFlavor($text); break;
						case "Rareté": $card->setRarity($text); break;
						case "Classe": $card->setPlayerClass($text); break;
						case "Type": $card->setFaction($text); break;
					}
				}
			});

		$this->cardService->save($card);

		return $card;
	}
}
