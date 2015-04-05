<?php

namespace Darkwood\HearthbreakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeckCard.
 *
 * @ORM\Table(name="deck_card",
 *  indexes={
 *    @ORM\Index(name="IDX_DECK", columns={"deck_id"}),
 *    @ORM\Index(name="IDX_CARD", columns={"card_id"}),
 *  },
 * 	uniqueConstraints={@ORM\UniqueConstraint(name="unique_deck_card", columns={"deck_id", "card_id"})}
 * )
 * @ORM\Entity(repositoryClass="Darkwood\HearthbreakerBundle\Repository\DeckCardRepository")
 */
class DeckCard
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Darkwood\HearthbreakerBundle\Entity\Deck
     *
     * @ORM\ManyToOne(targetEntity="\Darkwood\HearthbreakerBundle\Entity\Deck", inversedBy="cards")
     * @ORM\JoinColumn(name="deck_id", referencedColumnName="id")
     */
    protected $deck;

    /**
     * @var \Darkwood\HearthbreakerBundle\Entity\Card
     *
     * @ORM\ManyToOne(targetEntity="\Darkwood\HearthbreakerBundle\Entity\Card", inversedBy="decks")
     * @ORM\JoinColumn(name="card_id", referencedColumnName="id")
     */
    protected $card;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $quantity;

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
     * Set deck.
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\Deck $deck
     *
     * @return DeckCard
     */
    public function setDeck(\Darkwood\HearthbreakerBundle\Entity\Deck $deck = null)
    {
        $this->deck = $deck;

        return $this;
    }

    /**
     * Get deck.
     *
     * @return \Darkwood\HearthbreakerBundle\Entity\Deck
     */
    public function getDeck()
    {
        return $this->deck;
    }

    /**
     * Set card.
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\Card $card
     *
     * @return DeckCard
     */
    public function setCard(\Darkwood\HearthbreakerBundle\Entity\Card $card = null)
    {
        $this->card = $card;

        return $this;
    }

    /**
     * Get card.
     *
     * @return \Darkwood\HearthbreakerBundle\Entity\Card
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return DeckCard
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
