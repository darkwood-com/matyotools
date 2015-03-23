<?php

namespace Darkwood\HearthbreakerBundle\Services;

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

    public function syncCardList()
    {
        $crawler = $this->request('card_list', array('page' => 1));

		$slugs = array();
        $cards = $crawler
            ->filter('#liste_cartes .carte_galerie_container > a')
            ->each(function($card) use(&$slugs) {
                /** @var Crawler $card */
                $href = $card->attr('href');
				try {
					$match = $this->router->match($href);
					if($match['_route'] == 'card_detail') {
						$slugs[] = $match['slug'];
					}
				} catch (ResourceNotFoundException $e) {
				} catch (MethodNotAllowedException $e) {
				}
            });

		$this->syncCard($slugs[0]);
    }

	public function syncCard($slug)
	{
		$crawler = $this->request('card_detail', array('slug' => $slug));
		$crawler->filter('#content h3')->first()->text();
	}
}
