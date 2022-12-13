<?php

namespace ProjetNormandie\ForumBundle\Repository;

use ProjetNormandie\ForumBundle\Entity\Forum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class ForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forum::class);
    }

    /**
     * @param $forum
     * @return mixed
     */
    public function getParentData($forum)
    {
         $query = $this->createQueryBuilder('f')
            ->select('SUM(f.nbTopic) as nbTopic,SUM(f.nbMessage) as nbMessage, MAX(f.lastMessage) as lastMessage')
            ->where('f.parent = :forum')
            ->setParameter('forum', $forum);

        return $query->getQuery()->getResult()[0];
    }
}
