<?php

namespace Darkwood\HearthstonedecksBundle\Entity;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Card
 *
 * @ORM\Table(name="card_hearthstonedecks")
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class CardHearthstonedecks extends Card
{
    /**
     * @var string
     *
     * @ORM\Column(name="name_en", type="string", length=255)
     */
    private $nameEn;

	/**
	 * @ORM\OneToMany(targetEntity="Darkwood\HearthbreakerBundle\Entity\CardHearthbreaker", mappedBy="cardHearthstonedecks")
	 */
	private $cards;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->cards = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
     * Set name
     *
     * @param string $name
     * @return Card
     */
    public function setNameEn($nameEn)
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }

    /**
     * Add cards
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\CardHearthbreaker $cards
     * @return CardHearthstonedecks
     */
    public function addCard(\Darkwood\HearthbreakerBundle\Entity\CardHearthbreaker $cards)
    {
        $this->cards[] = $cards;

        return $this;
    }

    /**
     * Remove cards
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\CardHearthbreaker $cards
     */
    public function removeCard(\Darkwood\HearthbreakerBundle\Entity\CardHearthbreaker $cards)
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
