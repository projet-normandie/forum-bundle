<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\Topic;

class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    public function getForumData(Forum $forum)
    {
         $query = $this->createQueryBuilder('t')
            ->select('SUM(t.nbMessage) as nbMessage, COUNT(t) as nbTopic, MAX(t.lastMessage) as lastMessage')
            ->where('t.forum = :forum')
            ->setParameter('forum', $forum);

        return $query->getQuery()->getResult()[0];
    }
}
