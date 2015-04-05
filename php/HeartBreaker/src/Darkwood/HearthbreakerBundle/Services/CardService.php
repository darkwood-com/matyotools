<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\CardRepository;

class CardService
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
}
