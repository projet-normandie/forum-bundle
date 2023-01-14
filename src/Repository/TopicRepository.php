<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\Topic;

/**
 * Specific repository that serves the Topic entity.
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    /**
     * @param Forum $forum
     * @return mixed
     */
    public function getForumData(Forum $forum)
    {
         $query = $this->createQueryBuilder('t')
            ->select('SUM(t.nbMessage) as nbMessage, COUNT(t) as nbTopic, MAX(t.lastMessage) as lastMessage')
            ->where('t.forum = :forum')
            ->setParameter('forum', $forum);

        return $query->getQuery()->getResult()[0];
    }
}
