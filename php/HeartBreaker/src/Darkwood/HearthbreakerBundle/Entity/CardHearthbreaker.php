<?php

namespace Darkwood\HearthbreakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Card
 *
 * @ORM\Table(name="card_hearthbreaker")
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class CardHearthbreaker extends Card
{
	/**
	 * @ORM\OneToMany(targetEntity="Darkwood\HearthbreakerBundle\Entity\UserCard", mappedBy="card", cascade={"all"})
	 */
	private $users;

	/**
	 * Add users
	 *
	 * @param \Darkwood\HearthbreakerBundle\Entity\UserCard $users
	 * @return Card
	 */
	public function addUser(\Darkwood\HearthbreakerBundle\Entity\UserCard $users)
	{
		$this->users[] = $users;

		return $this;
	}

	/**
	 * Remove users
	 *
	 * @param \Darkwood\HearthbreakerBundle\Entity\UserCard $users
	 */
	public function removeUser(\Darkwood\HearthbreakerBundle\Entity\UserCard $users)
	{
		$this->users->removeElement($users);
	}

	/**
	 * Get users
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getUsers()
	{
		return $this->users;
	}
}
