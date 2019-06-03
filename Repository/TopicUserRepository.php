<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Specific repository that serves the Forum entity.
 */
class TopicUserRepository extends EntityRepository
{

    /**
     * @param      $user
     * @param null $forum
     */
    public function read($user, $forum = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->update('ProjetNormandie\ForumBundle\Entity\TopicUser', 'tu')
            ->set('tu.boolRead', ':boolRead')
            ->where('tu.user = :user')
            ->setParameter('boolRead', 1)
            ->setParameter('user', $user);
        if ($forum !== null) {
            $query->andWhere('tu.topic IN (SELECT t FROM ProjetNormandie\ForumBundle\Entity\Topic t WHERE t.forum = :forum)')
                ->setParameter('forum', $forum);
        }
        $query->getQuery()->execute();
    }
}
