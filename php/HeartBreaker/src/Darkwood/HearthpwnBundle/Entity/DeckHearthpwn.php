<?php

namespace Darkwood\HearthpwnBundle\Entity;

use Darkwood\HearthbreakerBundle\Entity\Deck;
use Doctrine\ORM\Mapping as ORM;

/**
 * Deck.
 *
 * @ORM\Table(name="deck_hearthpwn")
 * @ORM\Entity()
 */
class DeckHearthpwn extends Deck
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

    public function getSource()
    {
        return 'hearthpwn';
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }
}
