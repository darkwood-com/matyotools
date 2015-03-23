<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\DeckRepository;

class DeckService
{
	/**
	 * @var DeckRepository
	 */
    private $cardRepository;

	/**
	 * @param EntityManager $em
	 */
	public function __construct($em)
	{
		$this->cardRepository = $em->getRepository('HearthbreakerBundle:Deck');
	}

	public function findBySlug($slug)
	{
		$this->cardRepository->findOneBy(array('slug' => $slug));
	}
}
