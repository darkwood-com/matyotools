<?php

namespace Darkwood\HearthbreakerBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CardRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CardRepository extends EntityRepository
{
    public function findBySlug($slug, $source = null)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->andWhere('c.slug = :slug')->setParameter('slug', $slug)
        ;

        if($source) {
            $qb->andWhere('c INSTANCE OF :source')->setParameter('source', $source);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function count()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as nb')
        ;

        $count = $qb->getQuery()->getScalarResult();

        return $count[0]['nb'];
    }

    public function search($search)
    {
        $qb = $this->createQueryBuilder('c');

        if(isset($search['source']) && $search['source'] != null) {
            $qb->andWhere('c INSTANCE OF :source')->setParameter('source', $search['source']);
        }

        if (isset($search['title']) && $search['title'] != null) {
            $qb->andWhere('c.name LIKE :name')->setParameter('name', '%'.$search['title'].'%');
        }

        if (isset($search['type']) && $search['type'] != null) {
            $qb->andWhere('c.type = :type')->setParameter('type', $search['type']);
        }

        if (isset($search['class']) && $search['class'] != null) {
            $qb->andWhere('c.playerClass = :playerClass')->setParameter('playerClass', $search['class']);
        }

        if (isset($search['rarity']) && $search['rarity'] != null) {
            $qb->andWhere('c.rarity = :rarity')->setParameter('rarity', $search['rarity']);
        }

        if (isset($search['cost']) && $search['cost'] != null) {
            $qb->andWhere('c.cost > :cost')->setParameter('cost', $search['cost']);
        }

        if (isset($search['attack']) && $search['attack'] != null) {
            $qb->andWhere('c.attack > :attack')->setParameter('attack', $search['attack']);
        }

        if (isset($search['health']) && $search['health'] != null) {
            $qb->andWhere('c.health > :health')->setParameter('health', $search['health']);
        }

        return $qb->getQuery()->getResult();
    }
}
