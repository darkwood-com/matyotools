<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Doctrine\Common\Cache\Cache;
use Goutte\Client;
use GuzzleHttp\Subscriber\Cache\CacheStorage;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use Symfony\Component\DomCrawler\Crawler;

class ScrapperService
{
    private $host = 'http://www.hearthstone-decks.com';

    private $route_card_list = '/carte';
    private $route_card_detail = '/carte/voir/';

    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param $url
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    private function request($url)
    {
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
        $crawler = $this->request($this->host . $this->route_card_list);
        $cards = $crawler
            ->filter('#liste_cartes .carte_galerie_container > a')
            ->each(function($card) {
                /** @var Crawler $card */
                $href = $card->attr('href');
                if(strpos($href, $this->route_card_detail) == 0) {
                    $slug = substr($href, strlen($this->route_card_detail));
                }
            });
    }
}
