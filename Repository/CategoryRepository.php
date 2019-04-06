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
     * @return \Doctrine\ORM\Query
     */
    public function getHome()
    {
        $query = $this->createQueryBuilder('c')
            ->join('c.forums', 'f')
            ->join('f.lastMessage', 'm')
            ->join('m.user', 'u')
            ->addSelect('f')
            ->addSelect('m')
            ->addSelect('u')
            ->where('f.status = :status')
            ->setParameter('status', Forum::STATUS_PUBLIC);

        $query->orderBy('c.position', 'ASC')
            ->addOrderBy('f.position', 'ASC');


        return $query->getQuery();
    }
}
