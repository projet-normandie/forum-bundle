<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use ProjetNormandie\ForumBundle\Entity\Forum;

/**
 * Specific repository that serves the Category entity.
 */
class CategoryRepository extends EntityRepository
{
    /**
     * Finds category with forum
     *
     * @param $user
     *
     * @return Query
     */
    public function getHome($user = null): Query
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
                ->join('f.forumUser', 'fu', 'WITH', 'fu.user = :user')
                ->addSelect('fu')
                ->where(
                    $queryBuilder->expr()->orX(
                        'f.status = :status1',
                        '(f.status = :status2) AND (f.role IN (:roles))'
                    )
                )
                ->setParameter('status1', Forum::STATUS_PUBLIC)
                ->setParameter('status2', Forum::STATUS_PRIVATE)
                ->setParameter('user', $user)
                ->setParameter('roles', $user->getRoles());
        } else {
            $queryBuilder->where('f.status = :status')
                ->setParameter('status', Forum::STATUS_PUBLIC);
        }

        //echo $queryBuilder->getDQL(), "\n"; exit;
        $queryBuilder->orderBy('c.position', 'ASC')
            ->addOrderBy('f.position', 'ASC');


        return $queryBuilder->getQuery();
    }
}
