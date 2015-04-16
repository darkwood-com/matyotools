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

	public function getMatches()
	{
		return $this->wins + $this->losses;
	}

	public function getWinRate()
	{
		if($this->getMatches() > 0) {
			return $this->wins / $this->getMatches();
		}

		return 0;
	}
}
