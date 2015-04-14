<?php

namespace Darkwood\HearthstatsBundle\Entity;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Card.
 *
 * @ORM\Table(name="card_hearthstats")
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class CardHearthstats extends Card
{
    public function getSource()
    {
        return 'hearthstats';
    }
}
