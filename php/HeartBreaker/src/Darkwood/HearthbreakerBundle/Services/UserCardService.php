<?php

namespace Darkwood\HearthbreakerBundle\Services;

use Darkwood\HearthbreakerBundle\Entity\Card;
use Darkwood\HearthbreakerBundle\Entity\Deck;
use Darkwood\HearthbreakerBundle\Entity\UserCard;
use Darkwood\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Darkwood\HearthbreakerBundle\Repository\UserCardRepository;

class UserCardService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var CacheService
     */
    private $cacheService;

	/**
	 * @var CardService
	 */
	private $cardService;

    /**
     * @var UserCardRepository
     */
    private $userCardRepository;

	/**
	 * @param EntityManager $em
	 * @param CacheService $cacheService
	 * @param CardService $cardService
	 */
    public function __construct(EntityManager $em, CacheService $cacheService, CardService $cardService)
    {
        $this->em = $em;
		$this->userCardRepository = $em->getRepository('HearthbreakerBundle:UserCard');
        $this->cacheService = $cacheService;
		$this->cardService = $cardService;
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

    /**
     * @param User      $user
     * @param Deck|null $deck
     *
     * @return mixed
     */
    public function cardQuantity($user, $deck = null)
    {
        $key = implode('-', array('user-card-quantity', $user->getId()));

        return $this->cacheService->fetch($key, function () use ($user, $deck) {
            $cardsQuantity = array();
            $userCards = $this->findByUserAndDeck($user, $deck);
            foreach ($userCards as $userCard) {
				/* @var UserCard $userCard */
				$card = $userCard->getCard();

                $id = $card->getId();

                $isGolden = $userCard->getIsGolden() ? '1' : '0';
				if (!isset($cardsQuantity[$id])) {
					$cardsQuantity[$id] = array('0' => 0, '1' => 0, 'total' => 0);
				}
                $cardsQuantity[$id][$isGolden] = $userCard->getQuantity();
                $cardsQuantity[$id]['total'] += $userCard->getQuantity();

				/** @var Card[] $cardSiblings */
				$cardSiblings = $this->cardService->getSiblings($card);
				foreach($cardSiblings as $sibling)
				{
					$siblingId = $sibling->getId();
					if (!isset($cardsQuantity[$siblingId])) {
						$cardsQuantity[$siblingId] = array('0' => 0, '1' => 0, 'total' => 0);
					}

					$cardsQuantity[$siblingId]['0'] = max($cardsQuantity[$siblingId]['0'], $cardsQuantity[$id]['0']);
					$cardsQuantity[$siblingId]['1'] = max($cardsQuantity[$siblingId]['1'], $cardsQuantity[$id]['1']);
					$cardsQuantity[$siblingId]['total'] = max($cardsQuantity[$siblingId]['total'], $cardsQuantity[$id]['total']);
				}
			}

            return $cardsQuantity;
        }, 'user');
    }
}
