<?php

namespace Darkwood\HearthbreakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserCard
 *
 * @ORM\Table(name="user_card",
 *  indexes={
 *    @ORM\Index(name="IDX_DECK", columns={"user_id"}),
 *    @ORM\Index(name="IDX_CARD", columns={"card_id"}),
 *  },
 * 	uniqueConstraints={@ORM\UniqueConstraint(name="unique_user_card", columns={"user_id", "card_id"})}
 * )
 * @ORM\Entity(repositoryClass="Darkwood\HearthbreakerBundle\Repository\UserCardRepository")
 */
class UserCard
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @var \Darkwood\UserBundle\Entity\User
	 *
	 * @ORM\ManyToOne(targetEntity="\Darkwood\UserBundle\Entity\User", inversedBy="cards")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * @var \Darkwood\HearthbreakerBundle\Entity\Card
	 *
	 * @ORM\ManyToOne(targetEntity="\Darkwood\HearthbreakerBundle\Entity\Card", inversedBy="users")
	 * @ORM\JoinColumn(name="card_id", referencedColumnName="id")
	 */
	protected $card;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isGolden", type="boolean")
     */
    private $isGolden;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

	/**
	 * Set user
	 *
	 * @param \Darkwood\UserBundle\Entity\User $user
	 * @return UserCard
	 */
	public function setUser(\Darkwood\UserBundle\Entity\User $user = null)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Get user
	 *
	 * @return \Darkwood\UserBundle\Entity\User
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Set card
	 *
	 * @param \Darkwood\HearthbreakerBundle\Entity\Card $card
	 * @return UserCard
	 */
	public function setCard(\Darkwood\HearthbreakerBundle\Entity\Card $card = null)
	{
		$this->card = $card;

		return $this;
	}

	/**
	 * Get card
	 *
	 * @return \Darkwood\HearthbreakerBundle\Entity\Card
	 */
	public function getCard()
	{
		return $this->card;
	}

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return UserCard
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set isGolden
     *
     * @param boolean $isGolden
     * @return UserCard
     */
    public function setIsGolden($isGolden)
    {
        $this->isGolden = $isGolden;

        return $this;
    }

    /**
     * Get isGolden
     *
     * @return boolean
     */
    public function getIsGolden()
    {
        return $this->isGolden;
    }
}
