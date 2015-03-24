<?php

namespace Darkwood\HearthbreakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Card
 *
 * @ORM\Table(name="card",
 * 	uniqueConstraints={@ORM\UniqueConstraint(name="unique_slug", columns={"slug"})}
 * )
 * @ORM\Entity(repositoryClass="Darkwood\HearthbreakerBundle\Repository\CardRepository")
 */
class Card
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
	 * @ORM\Column(name="cost", type="integer", nullable=true)
	 */
	private $cost;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="type", type="string", length=255, nullable=true)
	 */
	private $type;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="rarity", type="string", length=255, nullable=true)
	 */
	private $rarity;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="faction", type="string", length=255, nullable=true)
	 */
	private $faction;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="race", type="string", length=255, nullable=true)
	 */
	private $race;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="playerClass", type="string", length=255, nullable=true)
	 */
	private $playerClass;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="text", type="string", length=255, nullable=true)
	 */
	private $text;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="flavor", type="string", length=255, nullable=true)
	 */
	private $flavor;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="attack", type="integer", nullable=true)
	 */
	private $attack;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="health", type="integer", nullable=true)
	 */
	private $health;

	/**
	 * @ORM\OneToMany(targetEntity="Darkwood\HearthbreakerBundle\Entity\DeckCard", mappedBy="card", cascade={"all"})
	 */
	private $decks;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->decks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Card
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
     * @return Card
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
	public function getCost()
	{
		return $this->cost;
	}

	/**
	 * @param int $cost
	 */
	public function setCost($cost)
	{
		$this->cost = $cost;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getRarity()
	{
		return $this->rarity;
	}

	/**
	 * @param string $rarity
	 */
	public function setRarity($rarity)
	{
		$this->rarity = $rarity;
	}

	/**
	 * @return string
	 */
	public function getFaction()
	{
		return $this->faction;
	}

	/**
	 * @param string $faction
	 */
	public function setFaction($faction)
	{
		$this->faction = $faction;
	}

	/**
	 * @return string
	 */
	public function getRace()
	{
		return $this->race;
	}

	/**
	 * @param string $race
	 */
	public function setRace($race)
	{
		$this->race = $race;
	}

	/**
	 * @return string
	 */
	public function getPlayerClass()
	{
		return $this->playerClass;
	}

	/**
	 * @param string $playerClass
	 */
	public function setPlayerClass($playerClass)
	{
		$this->playerClass = $playerClass;
	}

	/**
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @param string $text
	 */
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * @return string
	 */
	public function getFlavor()
	{
		return $this->flavor;
	}

	/**
	 * @param string $flavor
	 */
	public function setFlavor($flavor)
	{
		$this->flavor = $flavor;
	}

	/**
	 * @return int
	 */
	public function getAttack()
	{
		return $this->attack;
	}

	/**
	 * @param int $attack
	 */
	public function setAttack($attack)
	{
		$this->attack = $attack;
	}

	/**
	 * @return int
	 */
	public function getHealth()
	{
		return $this->health;
	}

	/**
	 * @param int $health
	 */
	public function setHealth($health)
	{
		$this->health = $health;
	}

    /**
     * Add decks
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\DeckCard $decks
     * @return Card
     */
    public function addDeck(\Darkwood\HearthbreakerBundle\Entity\DeckCard $decks)
    {
        $this->decks[] = $decks;

        return $this;
    }

    /**
     * Remove decks
     *
     * @param \Darkwood\HearthbreakerBundle\Entity\DeckCard $decks
     */
    public function removeDeck(\Darkwood\HearthbreakerBundle\Entity\DeckCard $decks)
    {
        $this->decks->removeElement($decks);
    }

    /**
     * Get decks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDecks()
    {
        return $this->decks;
    }

	public function getBuy($golden = false)
	{
		switch($this->rarity) {
			case 'Légendaire':
				return $golden ? 3200 : 1600;
				break;
			case 'Epique':
				return $golden ? 1600 : 400;
				break;
			case 'Rare':
				return $golden ? 800 : 100;
				break;
			case 'Commune':
				return $golden ? 400 : 40;
				break;
		}

		return 0;
	}

	public function getSell($golden = false)
	{
		switch($this->rarity) {
			case 'Légendaire':
				return $golden ? 1600 : 400;
				break;
			case 'Epique':
				return $golden ? 400 : 100;
				break;
			case 'Rare':
				return $golden ? 100 : 20;
				break;
			case 'Commune':
				return $golden ? 50 : 5;
				break;
		}

		return 0;
	}
}
