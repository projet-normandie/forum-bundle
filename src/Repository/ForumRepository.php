<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProjetNormandie\ForumBundle\Entity\Forum;

class ForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forum::class);
    }

    public function getParentData($forum)
    {
         $query = $this->createQueryBuilder('f')
            ->select('SUM(f.nbTopic) as nbTopic,SUM(f.nbMessage) as nbMessage, MAX(f.lastMessage) as lastMessage')
            ->where('f.parent = :forum')
            ->setParameter('forum', $forum);

        return $query->getQuery()->getResult()[0];
    }
}
