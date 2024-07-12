<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use ProjetNormandie\ForumBundle\Entity\Category;
use ProjetNormandie\ForumBundle\ValueObject\ForumStatus;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

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
                ->setParameter('status1', ForumStatus::PUBLIC)
                ->setParameter('status2', ForumStatus::PRIVATE)
                ->setParameter('user', $user)
                ->setParameter('roles', $user->getRoles());
        } else {
            $queryBuilder->where('f.status = :status')
                ->setParameter('status', ForumStatus::PUBLIC);
        }

        $queryBuilder->orderBy('c.position', 'ASC')
            ->addOrderBy('f.position', 'ASC');


        return $queryBuilder->getQuery();
    }
}
