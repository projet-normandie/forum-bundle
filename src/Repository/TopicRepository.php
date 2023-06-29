<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use ProjetNormandie\ForumBundle\Entity\Forum;

/**
 * Specific repository that serves the Topic entity.
 */
class TopicRepository extends EntityRepository
{
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
