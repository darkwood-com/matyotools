<?php

namespace Darkwood\HearthbreakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deck
 *
 * @ORM\Table(name="deck",
 * 	uniqueConstraints={@ORM\UniqueConstraint(name="unique_slug", columns={"slug"})}
 * )
 * @ORM\Entity(repositoryClass="Darkwood\HearthbreakerBundle\Repository\DeckRepository")
 */
class Deck
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
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="vote_up", type="integer", nullable=true)
	 */
	private $voteUp;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="vote_down", type="integer", nullable=true)
	 */
	private $voteDown;

	/**
	 * @ORM\OneToMany(targetEntity="Darkwood\HearthbreakerBundle\Entity\DeckCard", mappedBy="deck", cascade={"all"})
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Deck
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Deck
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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

    /**
     * Add cards
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\DeckCard $cards
     * @return Deck
     */
    public function addCard(\Darkwood\HearthbreakerBundle\Entity\DeckCard $cards)
    {
        $this->cards[] = $cards;

        return $this;
    }

    /**
     * Remove cards
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\DeckCard $cards
     */
    public function removeCard(\Darkwood\HearthbreakerBundle\Entity\DeckCard $cards)
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

	public function getBuy()
	{
		$cristal = 0;

		foreach($this->cards as $deckCard) {
			/** @var DeckCard $deckCard */
			$cristal += $deckCard->getCard()->getBuy() * $deckCard->getQuantity();
		}

		return $cristal;
	}

	public function getSell()
	{
		$cristal = 0;

		foreach($this->cards as $deckCard) {
			/** @var DeckCard $deckCard */
			$cristal += $deckCard->getCard()->getSell() * $deckCard->getQuantity();
		}

		return $cristal;
	}
}
