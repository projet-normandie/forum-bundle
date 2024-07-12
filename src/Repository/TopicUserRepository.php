<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Entity\TopicUser;

class TopicUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopicUser::class);
    }

    public function isRead($user, Topic $topic): bool
    {
        $topicUser = $this->findOneBy(['user' => $user, 'topic' => $topic]);
        return $topicUser->getBoolRead();
    }

    /**
     * @param       $user
     * @param Forum $forum
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countTopicNotRead($user, Forum $forum): int
    {
         $query = $this->createQueryBuilder('tu')
             ->select('COUNT(tu.id)')
             ->join('tu.topic', 't')
             ->where('t.forum = :forum')
             ->andWhere('tu.user = :user')
             ->andWhere('tu.boolRead = 0')
             ->setParameter('forum', $forum)
             ->setParameter('user', $user);

        return (int) $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param            $user
     * @param Topic|null $topic
     * @param Forum|null $forum
     * @return void
     */
    public function markAsRead($user, ?Topic $topic = null, ?Forum $forum = null): void
    {
        $query = $this->_em->createQueryBuilder()
            ->update('ProjetNormandie\ForumBundle\Entity\TopicUser', 'tu')
            ->set('tu.boolRead', true)
            ->where('tu.user = :user')
            ->setParameter('user', $user);
        if ($topic) {
            $query->andWhere('tu.topic = :topic')
                ->setParameter('topic', $topic);
        }
        if ($forum) {
            $query->andWhere('tu.topic IN (
                SELECT t FROM ProjetNormandie\ForumBundle\Entity\Topic t WHERE t.forum = :forum)')
                ->setParameter('forum', $forum);
        }
        $query->getQuery()->getResult();
    }
}
