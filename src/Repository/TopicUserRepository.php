<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\Topic;

class TopicUserRepository extends EntityRepository
{
    /**
     * @param $user
     * @throws Exception
     */
    public function init($user): void
    {
        $query ="INSERT INTO forum_topic_user (idTopic, idUser)
                 SELECT id, :idUser FROM forum_topic";
        $this->_em->getConnection()->executeStatement($query, array('idUser' => $user->getId()));
    }

    /**
     * @param       $user
     * @param Topic $topic
     * @return bool
     */
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
                SELECT t FROM ProjetNormandie\ForumBundle\Entity\Topic t WHERE t.forum = :forum)'
            )
                ->setParameter('forum', $forum);
        }
        $query->getQuery()->getResult();
    }
}
