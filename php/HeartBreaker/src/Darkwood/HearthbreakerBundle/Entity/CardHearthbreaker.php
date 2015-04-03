<?php

namespace Darkwood\HearthbreakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Card.
 *
 * @ORM\Table(name="card_hearthbreaker")
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class CardHearthbreaker extends Card
{
    /**
     * @var \Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks
     *
     * @ORM\ManyToOne(targetEntity="\Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks", inversedBy="cards")
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
     * Set cardHearthstonedecks.
     *
     * @param \Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks $cardHearthstonedecks
     *
     * @return CardHearthbreaker
     */
    public function setCardHearthstonedecks(\Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks $cardHearthstonedecks = null)
    {
        $this->cardHearthstonedecks = $cardHearthstonedecks;

        return $this;
    }

    /**
     * Get cardHearthstonedecks.
     *
     * @return \Darkwood\HearthstonedecksBundle\Entity\CardHearthstonedecks
     */
    public function getCardHearthstonedecks()
    {
        return $this->cardHearthstonedecks;
    }

    /**
     * Add users.
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\UserCard $users
     *
     * @return Card
     */
    public function addUser(\Darkwood\HearthbreakerBundle\Entity\UserCard $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users.
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\UserCard $users
     */
    public function removeUser(\Darkwood\HearthbreakerBundle\Entity\UserCard $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
