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
use GuzzleHttp\Exception\ClientException;
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

    private function requestRoute($name, $parameters = array(), $data = null)
    {
        $url = $this->router->generate($name, $parameters, true);

        return $data ? $this->client->request('POST', $url, $data) : $this->client->request('GET', $url);
    }

    public function syncDeckList($limit = null, $force = false)
    {
        $page = 1;
        $deckCount = 0;

        do {
            $crawler = $this->requestRoute('deck_list', array('page' => $page));

            $slugs = $crawler
                ->filter('.decklist .name a')
                ->each(function (Crawler $node) {
                    try {
                        $href = $node->attr('href');
                        $match = $this->router->match($href);
                        if ($match['_route'] == 'deck_detail') {
							$pos = strpos($match['slug'], '?');
							if($pos !== false)
							{
								$match['slug'] = substr($match['slug'], 0, $pos);
							}

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

            $hasNext = $crawler->filter('.pagination')->children()
                ->reduce(function (Crawler $node) {
                    return substr($node->text(), 0, 4) == 'Next';
                })->count() > 0;

            $page += 1;
        } while ($hasNext);

        return $deckCount;
    }

    public function syncCard($slug, $name, $image, $force = false)
    {
        $card = $this->cardService->findBySlug($slug, 'hearthstats');
        if (!$card) {
            $card = new CardHearthstats();
            $card->setSlug($slug);
        } elseif (!$force) {
            return $card;
        }

		$card->setName($name);

        if (!$card->getImageName()) {
			try {
				$match = $this->router->match($image);
				if ($match['_route'] == 'card_image_min') {
					$imageSrc = $this->router->generate('card_image', array('image' => $slug . '.png'), true);
					$guzzle = $this->client->getClient();
					$response = $guzzle->get($imageSrc);
					$filePath = tempnam(sys_get_temp_dir(), 'HB_');
					file_put_contents($filePath, $response->getBody());
					$card->setImage(new UploadedFile($filePath, $imageSrc, null, null, null, true));
				}
			} catch (ClientException $e) {
			} catch (ResourceNotFoundException $e) {
			} catch (MethodNotAllowedException $e) {
			}
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

		$name = $crawler->filter('.page-title')->html();
		$pos = strpos($name, '<small>');
		if($pos !== false)
		{
			$name = substr($name, 0, $pos);
		}
        $deck->setName($name);
		$deck->setWins(intval($crawler->filter('.win-count')->eq(3)->text()));
		$deck->setLosses(intval($crawler->filter('.win-count')->eq(4)->text()));

		$cards = $crawler
            ->filter('.deckbuilderWrapper .card')
            ->each(function (Crawler $node) use ($force) {
				$cardName = $node->filter('.name')->text();
				$cardSlug = strtolower($node->filter('.image')->attr('alt'));
				$cardImage = $node->filter('.image')->attr('src');

				return array(
					'card' => $this->syncCard($cardSlug, $cardName, $cardImage, $force),
					'quantity' => $node->filter('.qty')->text(),
				);
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
        $this->syncDeckList($limit);
    }
}
