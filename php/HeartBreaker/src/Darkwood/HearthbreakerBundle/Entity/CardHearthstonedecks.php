<?php

namespace Darkwood\HearthbreakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
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
}
