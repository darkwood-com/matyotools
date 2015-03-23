<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\CardRepository;

class CardService
{
	/**
	 * @var CardRepository
	 */
    private $cardRepository;

	/**
	 * @param EntityManager $em
	 */
    public function __construct($em)
    {
        $this->cardRepository = $em->getRepository('HearthbreakerBundle:Card');
    }

	public function findBySlug($slug)
	{
		$this->cardRepository->findOneBy(array('slug' => $slug));
	}
}
