<?php

namespace Darkwood\HearthbreakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Card.
 *
 * @ORM\Table(name="card_unity")
 * @ORM\Entity(repositoryClass="Darkwood\HearthbreakerBundle\Repository\CardUnityRepository")
 * @Vich\Uploadable
 */
class CardUnity
{
    /**
     * @var \Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks
     *
     * @ORM\OneToOne(targetEntity="\Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks", inversedBy="card")
     * @ORM\JoinColumn(name="card_hearthstonedecks_id", referencedColumnName="id")
     */
    protected $cardHearthstonedecks;

    /**
     * @ORM\OneToMany(targetEntity="Darkwood\HearthbreakerBundle\Entity\UserCard", mappedBy="card", cascade={"all"})
     */
    private $users;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set cardHearthstonedecks
     *
     * @param \Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks $cardHearthstonedecks
     * @return CardUnity
     */
    public function setCardHearthstonedecks(\Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks $cardHearthstonedecks = null)
    {
        $this->cardHearthstonedecks = $cardHearthstonedecks;

        return $this;
    }

    /**
     * Get cardHearthstonedecks
     *
     * @return \Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks 
     */
    public function getCardHearthstonedecks()
    {
        return $this->cardHearthstonedecks;
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
}
