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
    public function getSource()
    {
        return 'hearthstonedecks';
    }

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $nameEn;

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
}
