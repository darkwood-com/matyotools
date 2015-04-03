<?php

namespace Darkwood\HearthstonedecksBundle\Entity;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Card.
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
     * @ORM\OneToOne(targetEntity="Darkwood\HearthbreakerBundle\Entity\CardHearthbreaker", mappedBy="cardHearthstonedecks")
     */
    private $card;

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Card
     */
    public function setNameEn($nameEn)
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getNameEn()
    {
        return $this->nameEn;
    }

    /**
     * Set card
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\CardHearthbreaker $card
     * @return CardHearthstonedecks
     */
    public function setCard(\Darkwood\HearthbreakerBundle\Entity\CardHearthbreaker $card = null)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Get card
     *
     * @return \Darkwood\HearthbreakerBundle\Entity\CardHearthbreaker 
     */
    public function getCard()
    {
        return $this->card;
    }
}
