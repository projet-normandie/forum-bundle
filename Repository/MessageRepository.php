<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use ProjetNormandie\ForumBundle\Entity\Topic;

/**
 * Specific repository that serves the Message entity.
 */
class MessageRepository extends EntityRepository
{
    public function getPosition($message)
    {
        $qb = $this->createQueryBuilder('message')
            ->select('COUNT(message.id)')
            ->where('message.id <= :id')
            ->setParameter('id', $message->getId());

        return $qb->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @param Topic $topic
     * @return mixed
     */
    public function getTopicData(Topic $topic)
    {
         $query = $this->createQueryBuilder('m')
            ->select('COUNT(m) as nbMessage, MAX(m.id) as lastMessage')
            ->where('m.topic = :topic')
            ->setParameter('topic', $topic);

        return $query->getQuery()->getResult()[0];
    }
}
