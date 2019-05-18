<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use ProjetNormandie\ForumBundle\Entity\Forum;

/**
 * Specific repository that serves the Category entity.
 */
class CategoryRepository extends EntityRepository
{
    /**
     * Finds category with forum
     *
     *
     * @param User $user
     * @return \Doctrine\ORM\Query
     */
    public function getHome($user)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->join('c.forums', 'f')
            ->join('f.lastMessage', 'm')
            ->join('m.user', 'u')
            ->addSelect('f')
            ->addSelect('m')
            ->addSelect('u');


        if ($user !== null) {
            $queryBuilder
                ->where(
                    $queryBuilder->expr()->orX(
                        'f.status = :status1',
                        '(f.status = :status2) AND (f.role IN (:roles))'
                    )
                )
                ->setParameter('status1', Forum::STATUS_PUBLIC)
                ->setParameter('status2', Forum::STATUS_PRIVATE)
                ->setParameter('roles', $user->getRoles());
        } else {
            $queryBuilder->where('f.status = :status')
                ->setParameter('status', Forum::STATUS_PUBLIC);
        }

        $queryBuilder->orderBy('c.position', 'ASC')
            ->addOrderBy('f.position', 'ASC');


        return $queryBuilder->getQuery();
    }
}
