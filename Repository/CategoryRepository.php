<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;

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
            ->addSelect('u');

        $query->orderBy('c.position', 'ASC')
            ->addOrderBy('f.position', 'ASC');


        return $query->getQuery();
    }
}
