<?php

namespace Darkwood\HearthstatsBundle\Entity;

use Darkwood\HearthbreakerBundle\Entity\Deck;
use Doctrine\ORM\Mapping as ORM;

/**
 * Deck.
 *
 * @ORM\Table(name="deck_hearthstats")
 * @ORM\Entity()
 */
class DeckHearthstats extends Deck
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
    private $matches;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $wins;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $losses;

    public function getSource()
    {
        return 'hearthstats';
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
     * Set matches
     *
     * @param integer $matches
     * @return DeckHearthstats
     */
    public function setMatches($matches)
    {
        $this->matches = $matches;

        return $this;
    }

    /**
     * Get matches
     *
     * @return integer
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * Set wins
     *
     * @param integer $wins
     * @return DeckHearthstats
     */
    public function setWins($wins)
    {
        $this->wins = $wins;

        return $this;
    }

    /**
     * Get wins
     *
     * @return integer
     */
    public function getWins()
    {
        return $this->wins;
    }

    /**
     * Set losses
     *
     * @param integer $losses
     * @return DeckHearthstats
     */
    public function setLosses($losses)
    {
        $this->losses = $losses;

        return $this;
    }

    /**
     * Get losses
     *
     * @return integer
     */
    public function getLosses()
    {
        return $this->losses;
    }
}
