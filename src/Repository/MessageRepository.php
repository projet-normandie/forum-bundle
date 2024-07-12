<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\ForumBundle\Entity\Topic;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

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
     * @throws NonUniqueResultException
     */
    public function getLastMessageId(Topic $topic)
    {
         $qb = $this->createQueryBuilder('m')
            ->select('MAX(m.id) as lastMessage')
            ->where('m.topic = :topic')
            ->setParameter('topic', $topic);

        return $qb->getQuery()
            ->getOneOrNullResult();
    }
}
