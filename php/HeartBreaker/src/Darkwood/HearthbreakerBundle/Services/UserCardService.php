<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\UserCard;
use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\UserCardRepository;

class UserCardService
{
	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var UserCardRepository
	 */
	private $userCardRepository;

	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
		$this->userCardRepository = $em->getRepository('HearthbreakerBundle:UserCard');
	}

	/**
	 * Save a userCard
	 *
	 * @param UserCard $userCard
	 */
	public function save(UserCard $userCard)
	{
		$this->em->persist($userCard);
		$this->em->flush();
	}

	/**
	 * Remove one userCard
	 *
	 * @param UserCard $userCard
	 */
	public function remove(UserCard $userCard)
	{
		$this->em->remove($userCard);
		$this->em->flush();
	}

	/**
	 * @param $slug
	 * @return null|UserCard
	 */
	public function findByUserAndCard($user, $card, $isGolden = null)
	{
        return $this->userCardRepository->findByUserAndCard($user, $card, $isGolden);
	}
}
