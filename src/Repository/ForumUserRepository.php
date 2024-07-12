<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\ForumUser;

class ForumUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumUser::class);
    }

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
