<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use VideoGamesRecords\CoreBundle\Entity\Game;

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
}
