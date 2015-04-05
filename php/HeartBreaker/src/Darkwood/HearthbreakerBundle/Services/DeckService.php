<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\Deck;
use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\DeckRepository;
use Symfony\Component\DependencyInjection\ContainerAware;

class DeckService extends ContainerAware
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var DeckRepository
     */
    private $deckRepository;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->deckRepository = $em->getRepository('HearthbreakerBundle:Deck');
    }

    /**
     * Save a deck.
     *
     * @param Deck $deck
     */
    public function save(Deck $deck)
    {
        $this->em->persist($deck);
        $this->em->flush();
    }

    /**
     * Remove one deck.
     *
     * @param Deck $deck
     */
    public function remove(Deck $deck)
    {
        $this->em->remove($deck);
        $this->em->flush();
    }

    public function findAll()
    {
        return $this->deckRepository->findAll();
    }

    /**
     * @param $slug
     * @param null $source
     *
     * @return null|Deck
     */
    public function findBySlug($slug, $source = null)
    {
        return $this->deckRepository->findBySlug($slug, $source);
    }

    public function search($search)
    {
        return $this->deckRepository->search($search);
    }

    /**
     * @param Deck $deck
     * @return string
     */
    public function getUrl($deck)
    {
        /** @var \Symfony\Component\Routing\Router $router */
        $router = $this->container->get(sprintf('hb.%s.router', $deck->getSource()));
        return $router->generate('deck_detail', array('slug' => $deck->getSlug()), true);
    }
}
