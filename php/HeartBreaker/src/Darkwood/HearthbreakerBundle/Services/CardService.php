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
	 * Save a card
	 *
	 * @param Card $card
	 */
	public function save(Card $card)
	{
		$this->em->persist($card);
		$this->em->flush();
	}

	/**
	 * Remove one card
	 *
	 * @param Card $card
	 */
	public function remove(Card $card)
	{
		$this->em->remove($card);
		$this->em->flush();
	}

	/**
	 * @param $slug
	 * @return null|Card
	 */
	public function findBySlug($slug)
	{
		return $this->cardRepository->findOneBy(array('slug' => $slug));
	}
}
