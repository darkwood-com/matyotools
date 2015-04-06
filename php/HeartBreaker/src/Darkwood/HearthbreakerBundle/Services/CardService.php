<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Darkwood\HearthpwnBundle\Entity\CardHearthpwn;
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
     * @var CardRepository
     */
    private $cardRepository;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->cardRepository = $em->getRepository('HearthbreakerBundle:Card');
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
     * @return string
     */
    public function getUrl($card)
    {
        /** @var \Symfony\Component\Routing\Router $router */
        $router = $this->container->get(sprintf('hb.%s.router', $card->getSource()));
        return $router->generate('card_detail', array('slug' => $card->getSlug()), true);
    }

    /**
     * @param Card $iCard
     * @param Card $jCard
     */
    public function compare($iCard, $jCard)
    {
        $names = array_map(function($card) {
            if($card instanceof CardHearthstonedecks) {
                return $card->getNameEn();
            } else if($card instanceof CardHearthpwn) {
                return $card->getName();
            }
            return $card->getName();
        }, array($iCard, $jCard));

        return levenshtein($names[0], $names[1]);
    }

    public function identify()
    {
        $cards = $this->findAll();

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
                if($lev != -1 && $lev < 3) {
                    $keys[] = $key;
                }
            }

            if(count($keys) > 0) {
                $iCard->setIdentifier($id);
                foreach($keys as $key) {
                    $cards[$key]->setIdentifier($id);
                    unset($cards[$key]);
                }
                $id ++;
            }

            $iCard = array_shift($cards);
        }

        $this->em->flush();
    }
}
