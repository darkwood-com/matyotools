<?php

namespace Darkwood\HearthpwnBundle\Services;

use Darkwood\HearthbreakerBundle\Services\CardService;
use Darkwood\HearthbreakerBundle\Services\DeckCardService;
use Darkwood\HearthbreakerBundle\Services\DeckService;
use Darkwood\HearthpwnBundle\Entity\CardHearthpwn;
use Darkwood\HearthbreakerBundle\Entity\DeckCard;
use Darkwood\HearthpwnBundle\Entity\DeckHearthpwn;
use Darkwood\HearthbreakerBundle\Subscriber\Cache\CacheStorage;
use Doctrine\Common\Cache\Cache;
use Goutte\Client;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Router;

class ScrapperHearthpwnService
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

    /**
     * @var DeckCardService
     */
    private $deckCardService;

    public function __construct(Cache $cache, Router $router, CardService $cardService, DeckService $deckService, DeckCardService $deckCardService)
    {
        $this->cache = $cache;
        $this->router = $router;
        $this->cardService = $cardService;
        $this->deckService = $deckService;
        $this->deckCardService = $deckCardService;
    }

    private function guessDate($date)
    {
        $date = str_replace(array(
            'lundi', 'mardi', 'mercredi', 'jeudi',
            'vendredi', 'samedi', 'dimanche', 'janvier',
            'février', 'mars', 'avril', 'mai',
            'juin', 'juillet', 'août', 'septembre',
            'octobre', 'novembre', 'décembre',
        ), array(
            'Monday', 'Tuesday', 'Wednesday', 'Thursday',
            'Friday', 'Saturday', 'Sunday', 'January',
            'February', 'March', 'April', 'May',
            'June', 'July', 'August', 'September',
            'October', 'November', 'December',
        ), strtolower($date));

        return new \DateTime($date);
    }

    private function requestRoute($name, $parameters = array(), $data = null)
    {
        $url = $this->router->generate($name, $parameters, true);
        $client = $this->getClient($url, $data);

        return $data ? $client->request('POST', $url, $data) : $client->request('GET', $url);
    }

    private function getClient()
    {
        static $client = null;

        if (!$client) {
            $client = new Client();

            $guzzle = $client->getClient();
            $guzzle->setDefaultOption('debug', true);

            CacheSubscriber::attach($guzzle, array(
                'storage' => new CacheStorage($this->cache),
                'validate' => false,
                'can_cache' => function () {
                    return true;
                },
            ));

            $guzzle->getEmitter()->on(
                'complete',
                function (\GuzzleHttp\Event\CompleteEvent $event) {
                    $response = $event->getResponse();
                    $response->setHeader('Cache-Control', 'max-age=604800'); //1 week
                    //$response->setHeader('Cache-Control', 'max-age=86400'); //1 day
                },
                'first'
            );
        }

        return $client;
    }

    public function syncCardList($force = false)
    {
        $page = 1;
        do {
            $crawler = $this->requestRoute('card_list', array(
                'display' => 1,
                'page' => $page
            ));

            $crawler
                ->filter('#content table.listing .col-name > a')
                ->each(function (Crawler $node) use ($force) {
                    try {
                        $href = $node->attr('href');
                        $match = $this->router->match($href);
                        if ($match['_route'] == 'card_detail') {
                            $this->syncCard($match['slug'], $force);
                        }
                    } catch (ResourceNotFoundException $e) {
                    } catch (MethodNotAllowedException $e) {
                    }
                });

            $hasNext = $crawler->filter('#content .b-pagination-list')->children()
                ->reduce(function (Crawler $node) {
                    return $node->text() == 'Next';
                })->count() > 0;

            $page += 1;
        } while ($hasNext);
    }

    public function syncDeckList($force = false)
    {
        $page = 1;
        do {
            $crawler = $this->requestRoute('deck_search', array(), array(
                'etape' => 'RechercheDecks',
                'colonne' => '',
                'ordre' => 'undefined',
                'page_demandee' => $page,
                'keyword' => null,
                'auteur' => null,
                'classe' => null,
                'top' => 'semaine',
                'extension' => 'undefined',
            ));

            $crawler
                ->filter('.nom_deck > a')
                ->each(function (Crawler $node) use (&$slugs, $force) {
                    try {
                        $href = $node->attr('href');
                        $match = $this->router->match($href);
                        if ($match['_route'] == 'deck_detail') {
                            $this->syncDeck($match['slug'], $force);
                        }
                    } catch (ResourceNotFoundException $e) {
                    } catch (MethodNotAllowedException $e) {
                    }
                });

            $hasNext = $crawler->filter('.pagination')->children()
                    ->reduce(function (Crawler $node) {
                        return $node->text() == 'Suiv';
                    })->count() > 0;

            $page += 1;
        } while ($hasNext);
    }

    public function syncCard($slug, $force = false)
    {
        $card = $this->cardService->findBySlug($slug, 'hearthpwn');
        if (!$card) {
            $card = new CardHearthpwn();
            $card->setSlug($slug);
        } elseif (!$force) {
            return $card;
        }

        $crawler = $this->requestRoute('card_detail', array('slug' => $slug));

        $card->setName($crawler->filter('#content .details h2.caption')->first()->text());
        $textNode = $crawler->filter('#content .details .card-info p');
        if(count($textNode)) {
            $card->setText($textNode->text());
        }
        $flavorNode = $crawler->filter('#content .details .card-flavor-text p');
        if(count($flavorNode)) {
            $card->setFlavor($flavorNode->first()->text());
        }

        $crawler
            ->filter('#content .details .infobox li')
            ->each(function (Crawler $node, $i) use ($card) {
                $text = trim($node->text());

                if(preg_match('/^Type: (.*)$/', $text, $m)) {
                    $card->setType($m[1]);
                } else if(preg_match('/^Rarity: (.*)$/', $text, $m)) {
                    $card->setRarity($m[1]);
                } else if(preg_match('/^Race: (.*)$/', $text, $m)) {
                    $card->setRace($m[1]);
                } else if(preg_match('/^Class: (.*)$/', $text, $m)) {
                    $card->setPlayerClass($m[1]);
                } else if(preg_match('/^Faction: (.*)$/', $text, $m)) {
                    $card->setFaction($m[1]);
                }
            });

        if (!$card->getImageName()) {
            $imageSrc = trim($crawler->filter('#content .details .hscard-static')->first()->attr('src'));
            $guzzle = $this->getClient()->getClient();
            $response = $guzzle->get($imageSrc);
            $filePath = tempnam(sys_get_temp_dir(), 'HB_');
            file_put_contents($filePath, $response->getBody());
            $card->setImage(new UploadedFile($filePath, $imageSrc, null, null, null, true));
        }

        $card->setSyncedAt(new \DateTime());
        $this->cardService->save($card);

        return $card;
    }

    public function syncDeck($slug, $force = false)
    {
        $deck = $this->deckService->findBySlug($slug, 'hearthpwn');
        if (!$deck) {
            $deck = new DeckHearthpwn();
            $deck->setSlug($slug);
        } elseif (!$force) {
            return $deck;
        }

        $crawler = $this->requestRoute('deck_detail', array('slug' => $slug));

        $deck->setName($crawler->filter('#content h3')->first()->text());

        $attr = null;
        $crawler
            ->filter('#creation-deck-etape1 td')
            ->each(function (Crawler $node, $i) use ($deck, &$attr) {
                $text = trim($node->text());
                if ($i % 2 == 0) {
                    $attr = $text;
                } else {
                    switch ($attr) {
                        case 'Note':
                            $deck->setVoteUp(intval($node->filter('.up_vert')->text()));
                            $deck->setVoteDown(intval($node->filter('.up_rouge')->text()));
                            break;
                        case 'Création':
                            $deck->setCreatedAt($this->guessDate($text));
                            break;
                        case 'Mise à jour':
                            $deck->setUpdatedAt($this->guessDate($text));
                            break;
                    }
                }
            });

        $crawler
            ->filter('#liste_cartes tbody tr')
            ->each(function (Crawler $node, $i) use ($deck, &$attr, $force) {
                try {
                    $href = $node->filter('a')->first()->attr('href');
                    $match = $this->router->match($href);
                    if ($match['_route'] == 'card_detail') {
                        $card = $this->syncCard($match['slug'], $force);
                        $quantity = intval($node->filter('td')->first()->text());

                        $deckCard = $this->deckCardService->findByDeckAndCard($deck, $card);
                        if (!$deckCard) {
                            $deckCard = new DeckCard();
                            $deckCard->setDeck($deck);
                            $deckCard->setCard($card);
                        } elseif (!$force) {
                            return;
                        }

                        $deckCard->setQuantity($quantity);
                        $deck->addCard($deckCard);
                    }
                } catch (ResourceNotFoundException $e) {
                } catch (MethodNotAllowedException $e) {
                }
            });

        $deck->setSyncedAt(new \DateTime());
        $this->deckService->save($deck);

        return $deck;
    }
}
