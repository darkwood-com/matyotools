<?php

namespace Darkwood\HearthstonedecksBundle\Entity;

use Darkwood\HearthbreakerBundle\Entity\Deck;
use Doctrine\ORM\Mapping as ORM;

/**
 * Deck.
 *
 * @ORM\Table(name="deck_hearthstonedecks")
 * @ORM\Entity()
 */
class DeckHearthstonedecks extends Deck
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="vote_up", type="integer", nullable=true)
     */
    private $voteUp;

    /**
     * @var int
     *
     * @ORM\Column(name="vote_down", type="integer", nullable=true)
     */
    private $voteDown;

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
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
    public function getVoteUp()
    {
        return $this->voteUp;
    }

    /**
     * @param int $voteUp
     */
    public function setVoteUp($voteUp)
    {
        $this->voteUp = $voteUp;
    }

    /**
     * @return int
     */
    public function getVoteDown()
    {
        return $this->voteDown;
    }

    /**
     * @param int $voteDown
     */
    public function setVoteDown($voteDown)
    {
        $this->voteDown = $voteDown;
    }
}
