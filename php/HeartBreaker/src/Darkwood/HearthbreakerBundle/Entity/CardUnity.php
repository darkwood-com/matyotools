<?php

namespace Darkwood\HearthbreakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Card.
 *
 * @ORM\Table(name="card_unity")
 * @ORM\Entity(repositoryClass="Darkwood\HearthbreakerBundle\Repository\CardUnityRepository")
 */
class CardUnity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Darkwood\HearthbreakerBundle\Entity\UserCard", mappedBy="card", cascade={"all"})
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Darkwood\HearthbreakerBundle\Entity\Card", mappedBy="card")
     */
    private $cards;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add users
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\UserCard $users
     * @return CardUnity
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

    /**
     * Add cards
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\Card $cards
     * @return CardUnity
     */
    public function addCard(\Darkwood\HearthbreakerBundle\Entity\Card $cards)
    {
        $this->cards[] = $cards;

        return $this;
    }

    /**
     * Remove cards
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\Card $cards
     */
    public function removeCard(\Darkwood\HearthbreakerBundle\Entity\Card $cards)
    {
        $this->cards->removeElement($cards);
    }

    /**
     * Get cards
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCards()
    {
        return $this->cards;
    }
}
