<?php

namespace Darkwood\HearthstatsBundle\Services;

use Darkwood\HearthbreakerBundle\Events;
use Darkwood\HearthbreakerBundle\Services\CardService;
use Darkwood\HearthbreakerBundle\Services\DeckCardService;
use Darkwood\HearthbreakerBundle\Services\DeckService;
use Darkwood\HearthstatsBundle\Entity\CardHearthstats;
use Darkwood\HearthbreakerBundle\Entity\DeckCard;
use Darkwood\HearthstatsBundle\Entity\DeckHearthstats;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Router;

class ScrapperHearthstatsService
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var Client
     */
    private $client;

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

    public function __construct(EventDispatcherInterface $dispatcher, Client $client, Router $router, CardService $cardService, DeckService $deckService, DeckCardService $deckCardService)
    {
        $this->dispatcher = $dispatcher;
        $this->client = $client;
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

        return $data ? $this->client->request('POST', $url, $data) : $this->client->request('GET', $url);
    }

    public function syncCardList($force = false)
    {
        $page = 1;
        do {
            $crawler = $this->requestRoute('card_list', array(
                'display' => 1,
                'page' => $page,
            ));

            $crawler
                ->filter('#content table.listing .col-name a')
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

            $hasNext = $crawler->filter('#content .paging-list')->children()
                ->reduce(function (Crawler $node) {
                    return $node->text() == 'Next';
                })->count() > 0;

            $page += 1;
        } while ($hasNext);
    }

    public function syncDeckList($limit = null, $force = false)
    {
        /*$page = 1;
        $deckCount = 0;

        do {
            $crawler = $this->requestRoute('deck_list', array('page' => $page));

            $slugs = $crawler
                ->filter('#content table.listing .col-name a')
                ->each(function (Crawler $node) {
                    try {
                        $href = $node->attr('href');
                        $match = $this->router->match($href);
                        if ($match['_route'] == 'deck_detail') {
                            return $match['slug'];
                        }
                    } catch (ResourceNotFoundException $e) {
                    } catch (MethodNotAllowedException $e) {
                    }

                    return false;
                });
            $slugs = array_filter($slugs);
            foreach ($slugs as $slug) {
                if ($limit && $deckCount >= $limit) {
                    return $deckCount;
                }

                $this->syncDeck($slug, $force);
                $deckCount ++;
            }

            $hasNext = $crawler->filter('#content .paging-list')->children()
                ->reduce(function (Crawler $node) {
                    return $node->text() == 'Next';
                })->count() > 0;

            $page += 1;
        } while ($hasNext);

        return $deckCount;*/
    }

    public function syncCard($slug, $force = false)
    {
        $card = $this->cardService->findBySlug($slug, 'hearthstats');
        if (!$card) {
            $card = new CardHearthstats();
            $card->setSlug($slug);
        } elseif (!$force) {
            return $card;
        }

        $crawler = $this->requestRoute('card_detail', array('slug' => $slug));

        $card->setName($crawler->filter('#content .details h2')->text());
        $textNode = $crawler->filter('#content .details .card-info p');
        if (count($textNode)) {
            $card->setText($textNode->text());
        }
        $flavorNode = $crawler->filter('#content .details .card-flavor-text p');
        if (count($flavorNode)) {
            $card->setFlavor($flavorNode->text());
        }

        $crawler
            ->filter('#content .details .infobox li')
            ->each(function (Crawler $node, $i) use ($card) {
                $text = trim($node->text());

                if (preg_match('/^Type: (.*)$/', $text, $m)) {
                    $card->setType($m[1]);
                } elseif (preg_match('/^Rarity: (.*)$/', $text, $m)) {
                    $card->setRarity($m[1]);
                } elseif (preg_match('/^Race: (.*)$/', $text, $m)) {
                    $card->setRace($m[1]);
                } elseif (preg_match('/^Class: (.*)$/', $text, $m)) {
                    $card->setPlayerClass($m[1]);
                } elseif (preg_match('/^Faction: (.*)$/', $text, $m)) {
                    $card->setFaction($m[1]);
                }
            });

        if (!$card->getImageName()) {
            $imageSrc = trim($crawler->filter('#content .details .hscard-static')->attr('src'));
            $guzzle = $this->client->getClient();
            $response = $guzzle->get($imageSrc);
            $filePath = tempnam(sys_get_temp_dir(), 'HB_');
            file_put_contents($filePath, $response->getBody());
            $card->setImage(new UploadedFile($filePath, $imageSrc, null, null, null, true));
        }

        $card->setSyncedAt(new \DateTime());
        $this->cardService->save($card);
        $this->dispatcher->dispatch(Events::SYNC_CARD, new GenericEvent($card));

        return $card;
    }

    public function syncDeck($slug, $force = false)
    {
        $deck = $this->deckService->findBySlug($slug, 'hearthstats');
        if (!$deck) {
            $deck = new DeckHearthstats();
            $deck->setSlug($slug);
        } elseif (!$force) {
            return $deck;
        }

        $crawler = $this->requestRoute('deck_detail', array('slug' => $slug));

        $deck->setName($crawler->filter('#content .details h2')->text());
        $deck->setRating(intval($crawler->filter('#content .details .t-deck-rating .rating-average')->text()));
        $deck->setUpdatedAt($this->guessDate($crawler->filter('#content .details .t-deck-header .standard-date')->text()));

        $cards = $crawler
            ->filter('#content .details .t-deck-details-card-list .col-name')
            ->each(function (Crawler $node) use ($force) {
                try {
                    $href = $node->filter('a')->attr('href');
                    $match = $this->router->match($href);
                    if ($match['_route'] == 'card_detail') {
                        return array(
                            'card' => $this->syncCard($match['slug'], $force),
                            'quantity' => intval(substr($node->text(), -3, 1)),
                        );
                    }
                } catch (ResourceNotFoundException $e) {
                } catch (MethodNotAllowedException $e) {
                }

                return false;
            });
        $cards = array_filter($cards);
        $cards = array_reduce($cards, function($carry , $item) {
            $cardId = $item['card']->getId();
            if(isset($carry[$cardId])) {
                $carry[$cardId]['quantity'] += $item['quantity'];
            } else {
                $carry[$cardId] = $item;
            }
            return $carry;
        }, array());

        foreach($cards as $item) {
            $deckCard = $this->deckCardService->findByDeckAndCard($deck, $item['card']);
            if (!$deckCard) {
                $deckCard = new DeckCard();
                $deckCard->setDeck($deck);
                $deckCard->setCard($item['card']);
            } elseif (!$force) {
                continue;
            }

            $deckCard->setQuantity($item['quantity']);
            $deck->addCard($deckCard);
        }

        $deck->setSyncedAt(new \DateTime());
        $this->deckService->save($deck);
        $this->dispatcher->dispatch(Events::SYNC_CARD, new GenericEvent($deck));

        return $deck;
    }

    public function sync($limit = null)
    {
        $this->syncDeckList($limit);
    }
}
