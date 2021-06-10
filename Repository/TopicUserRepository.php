<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Specific repository that serves the Forum entity.
 */
class TopicUserRepository extends EntityRepository
{

    /**
     * @param $user
     * @throws Exception
     */
    public function init($user)
    {
        $query ="INSERT INTO forum_topic_user (idTopic, idUser)
                 SELECT id, :idUser FROM forum_topic";
        $this->_em->getConnection()->executeStatement($query, array('idUser' => $user->getId()));
    }

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
            $query->andWhere(
                'tu.topic IN (SELECT t FROM ProjetNormandie\ForumBundle\Entity\Topic t WHERE t.forum = :forum)'
            )
                ->setParameter('forum', $forum);
        }
        $query->getQuery()->execute();
    }

    /**
     * @param $topic
     */
    public function setNotRead($topic)
    {
         $qb = $this->_em->createQueryBuilder();
         $query = $qb->update('ProjetNormandie\ForumBundle\Entity\TopicUser', 'tu')
            ->set('tu.boolRead', ':boolRead')
            ->where('tu.user != :user')
            ->andWhere('tu.topic = :topic')
            ->setParameter('boolRead', 0)
            ->setParameter('topic', $topic)
            ->setParameter('user', $topic->getLastMessage()->getUser());

        $query->getQuery()->execute();
    }

    /**
     * @param $forum
     * @param $user
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countNotRead($forum, $user)
    {
         $query = $this->createQueryBuilder('tu')
             ->select('COUNT(tu.id)')
             ->join('tu.topic')
             ->where('tu.forum = :forum')
             ->andWhere('tu.user = :user')
             ->andWhere('tu.boolRead = 0')
             ->setParameter('forum', $forum)
             ->setParameter('user', $user);

        return $query->getQuery()->getSingleScalarResult();
    }
}
