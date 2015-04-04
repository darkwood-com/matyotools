<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\CardUnity;
use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\CardUnityRepository;

class CardUnityService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var CardUnityRepository
     */
    private $cardUnityRepository;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->cardUnityRepository = $em->getRepository('HearthbreakerBundle:CardUnity');
    }

    /**
     * Save a cardUnity.
     *
     * @param CardUnity $cardUnity
     */
    public function save(CardUnity $cardUnity)
    {
        $this->em->persist($cardUnity);
        $this->em->flush();
    }

    /**
     * Remove one cardUnity.
     *
     * @param CardUnity $cardUnity
     */
    public function remove(CardUnity $cardUnity)
    {
        $this->em->remove($cardUnity);
        $this->em->flush();
    }
}
