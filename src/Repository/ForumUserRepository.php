<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use ProjetNormandie\ForumBundle\Entity\Forum;

class ForumUserRepository extends EntityRepository
{
    /**
     * @param       $user
     * @param Forum $parent
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countSubForumNotRead($user, Forum $parent): int
    {
         $query = $this->createQueryBuilder('fu')
             ->select('COUNT(fu.id)')
             ->join('fu.forum', 'f')
             ->where('f.parent = :parent')
             ->andWhere('fu.user = :user')
             ->andWhere('fu.boolRead = 0')
             ->setParameter('parent', $parent)
             ->setParameter('user', $user);
        return (int) $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param            $user
     * @param Forum|null $forum
     * @return void
     */
    public function markAsRead($user, ?Forum $forum = null): void
    {
        $query = $this->_em->createQueryBuilder()
            ->update('ProjetNormandie\ForumBundle\Entity\ForumUser', 'fu')
            ->set('fu.boolRead', true)
            ->where('fu.user = :user')
            ->setParameter('user', $user);

        if ($forum) {
            $query->andWhere('fu.forum = :forum')
                ->setParameter('forum', $forum);
        }
        $query->getQuery()->getResult();
    }
}
