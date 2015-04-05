<?php

namespace Darkwood\HearthpwnBundle\Entity;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Card.
 *
 * @ORM\Table(name="card_hearthpwn")
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class CardHearthpwn extends Card
{
    public function getSource()
    {
        return 'hearthpwn';
    }
}
