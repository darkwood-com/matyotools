<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\DeckCard;
use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\DeckCardRepository;

class DeckCardService
{
	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var DeckCardRepository
	 */
	private $deckCardRepository;

	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->deckCardRepository = $em->getRepository('HearthbreakerBundle:DeckCard');
	}

	/**
	 * Save a deckCard
	 *
	 * @param DeckCard $deckCard
	 */
	public function save(DeckCard $deckCard)
	{
		$this->em->persist($deckCard);
		$this->em->flush();
	}

	/**
	 * Remove one deckCard
	 *
	 * @param DeckCard $deckCard
	 */
	public function remove(DeckCard $deckCard)
	{
		$this->em->remove($deckCard);
		$this->em->flush();
	}

	/**
	 * @param $slug
	 * @return null|DeckCard
	 */
	public function findByDeckAndCard($deck, $card)
	{
		return $this->deckCardRepository->findOneBy(array('deck' => $deck, 'card' => $card));
	}
}