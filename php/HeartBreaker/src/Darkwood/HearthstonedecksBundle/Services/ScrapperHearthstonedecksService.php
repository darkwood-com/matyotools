<?php

namespace Darkwood\HearthstonedecksBundle\Services;

use Darkwood\HearthbreakerBundle\Events;
use Darkwood\HearthbreakerBundle\Services\CardService;
use Darkwood\HearthbreakerBundle\Services\DeckCardService;
use Darkwood\HearthbreakerBundle\Services\DeckService;
use Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks;
use Darkwood\HearthbreakerBundle\Entity\DeckCard;
use Darkwood\HearthstonedecksBundle\Entity\DeckHearthstonedecks;
use Darkwood\HearthbreakerBundle\Subscriber\Cache\CacheStorage;
use Doctrine\Common\Cache\Cache;
use Goutte\Client;
use GuzzleHttp\Subscriber\Cache\CacheSubscriber;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Router;

class ScrapperHearthstonedecksService
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
            $crawler = $this->requestRoute('card_list', array('page' => $page));

            $crawler
                ->filter('#liste_cartes .carte_galerie_container > a')
                ->each(function (Crawler $node) use (&$slugs, $force) {
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

            $cardsNumber = intval($crawler->filter('#liste_cartes strong')->text());
            $hasNext = $crawler->filter('#liste_cartes .pagination')->children()
                ->reduce(function (Crawler $node) {
                    return $node->text() == 'Suiv';
                })->count() > 0;

            $page += 1;
        } while ($hasNext && ($this->cardService->count('hearthstonedecks') < $cardsNumber || $force));
    }

    public function syncDeckList($limit = null, $force = false)
    {
        $page = 1;
        $deckCount = 0;

        do {
            $crawler = $this->requestRoute('deck_list', array(), array(
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

            $slugs = $crawler
                ->filter('.nom_deck > a')
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
            foreach($slugs as $slug)
            {
                if($limit && $deckCount >= $limit)
                {
                    return $deckCount;
                }

                $this->syncDeck($slug, $force);
                $deckCount ++;
            }

            $hasNext = $crawler->filter('.pagination')->children()
                    ->reduce(function (Crawler $node) {
                        return $node->text() == 'Suiv';
                    })->count() > 0;

            $page += 1;
        } while ($hasNext);

        return $deckCount;
    }

    public function syncCard($slug, $force = false)
    {
        $card = $this->cardService->findBySlug($slug, 'hearthstonedecks');
        if (!$card) {
            $card = new CardHearthstonedecks();
            $card->setSlug($slug);
        } elseif (!$force) {
            return $card;
        }

        $crawler = $this->requestRoute('card_detail', array('slug' => $slug));

        $attr = null;
        $crawler
            ->filter('#informations-cartes td')
            ->each(function (Crawler $node, $i) use ($card, &$attr) {
                $text = trim($node->text());
                if ($i % 2 == 0) {
                    $attr = $text;
                } else {
                    switch ($attr) {
                        case 'Nom': $card->setName($text); break;
                        case 'Nom original': $card->setNameEn($text); break;
                        case 'Coût en mana': $card->setCost(intval($text)); break;
                        case 'Attaque': $card->setAttack(intval($text)); break;
                        case 'Vie': $card->setHealth(intval($text)); break;
                        case 'Race': $card->setRace($text); break;
                        case 'Description': $card->setText($text); break;
                        case "Texte d'ambiance": $card->setFlavor($text); break;
                        case 'Rareté': $card->setRarity($text); break;
                        case 'Classe': $card->setPlayerClass($text); break;
                        case 'Type': $card->setType($text); break;
                    }
                }
            });

        if (!$card->getImageName()) {
            $imageSrc = trim($crawler->filter('#visuelcarte')->attr('src'));
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
        $deck = $this->deckService->findBySlug($slug, 'hearthstonedecks');
        if (!$deck) {
            $deck = new DeckHearthstonedecks();
            $deck->setSlug($slug);
        } elseif (!$force) {
            return $deck;
        }

        $crawler = $this->requestRoute('deck_detail', array('slug' => $slug));

        $deck->setName($crawler->filter('#content h3')->text());

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

        $cards = $crawler
            ->filter('#liste_cartes tbody tr')
            ->each(function (Crawler $node) use ($force) {
                try {
                    $href = $node->filter('a')->attr('href');
                    $match = $this->router->match($href);
                    if ($match['_route'] == 'card_detail') {
                        return array(
                            'card' => $this->syncCard($match['slug'], $force),
                            'quantity' => intval($node->filter('td')->text()),
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
        $this->dispatcher->dispatch(Events::SYNC_DECK, new GenericEvent($deck));

        return $deck;
    }

    public function sync($limit = null)
    {
        $this->syncCardList();
        $this->syncDeckList($limit);
    }
}
