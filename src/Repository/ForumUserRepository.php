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

    /**
     * @param            $user
     * @param Forum|null $forum
     * @return void
     */
    public function markAsRead($user, ?Forum $forum = null): void
    {
        $query = $this->_em->createQueryBuilder()
            ->update('ProjetNormandie\ForumBundle\Entity\ForumUser', 'fu')
            ->set('fu.isRead', true)
            ->where('fu.user = :user')
            ->setParameter('user', $user);

        if ($forum) {
            $query->andWhere('fu.forum = :forum')
                ->setParameter('forum', $forum);
        }
        $query->getQuery()->getResult();
    }
}
