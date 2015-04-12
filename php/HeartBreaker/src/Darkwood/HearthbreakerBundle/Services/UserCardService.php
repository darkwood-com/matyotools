<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\UserCard;
use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\UserCardRepository;

class UserCardService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var UserCardRepository
     */
    private $userCardRepository;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->userCardRepository = $em->getRepository('HearthbreakerBundle:UserCard');
    }

    /**
     * Save a userCard.
     *
     * @param UserCard $userCard
     */
    public function save(UserCard $userCard)
    {
        $this->em->persist($userCard);
        $this->em->flush();
    }

    /**
     * Remove one userCard.
     *
     * @param UserCard $userCard
     */
    public function remove(UserCard $userCard)
    {
        $this->em->remove($userCard);
        $this->em->flush();
    }

    /**
     * @param $user
     * @param $card
     * @param null $isGolden
     *
     * @return mixed
     */
    public function findByUserAndCard($user, $card, $isGolden = null)
    {
        return $this->userCardRepository->findByUserAndCard($user, $card, $isGolden);
    }

    public function findByUser($user, $isGolden = null)
    {
        return $this->userCardRepository->findByUser($user, $isGolden);
    }

    public function findByUserAndDeck($user, $deck = null, $isGolden = null)
    {
        return $this->userCardRepository->findByUserAndDeck($user, $deck, $isGolden);
    }

    public function findOneByUserAndCard($user, $card, $isGolden = null)
    {
        return $this->userCardRepository->findOneByUserAndCard($user, $card, $isGolden);
    }

    public function findAll()
    {
        return $this->userCardRepository->findAll();
    }

    public function cardQuantity($user, $deck = null)
    {
        $cardsQuantity = array();
        $userCards = $this->findByUserAndDeck($user, $deck);
        foreach ($userCards as $userCard) {
            /* @var UserCard $userCard */
            $id = $userCard->getCard()->getId();

            if (!isset($cardsQuantity[$id])) {
                $cardsQuantity[$id] = array('0' => 0, '1' => 0, 'total' => 0);
            }

            $isGolden = $userCard->getIsGolden() ? '1' : '0';
            $cardsQuantity[$id][$isGolden] = $userCard->getQuantity();
            $cardsQuantity[$id]['total'] += $userCard->getQuantity();
        }

        return $cardsQuantity;
    }
}
