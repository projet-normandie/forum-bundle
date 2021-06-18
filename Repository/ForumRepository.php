<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Specific repository that serves the Forum entity.
 */
class ForumRepository extends EntityRepository
{

    public function getParentData($forum)
    {
         $query = $this->createQueryBuilder('f')
            ->select('SUM(f.nbTopic) as nbTopic,SUM(f.nbMessage) as nbMessage, MAX(f.lastMessage) as lastMessage')
            ->where('f.parent = :forum')
            ->setParameter('forum', $forum);

        return $query->getQuery()->getResult()[0];
    }
}
