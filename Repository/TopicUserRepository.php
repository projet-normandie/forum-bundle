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
     * @param $user
     * @throws Exception
     */
    public function readAll($user)
    {
        $this->_em->getConnection()->executeStatement(
            "UPDATE forum_topic_user SET boolRead = 1 WHERE idUser=:idUser",
            ['idUser' => $user->getId()]
        );
    }

    /**
     * @param $user
     * @param $forum
     * @throws Exception
     */
    public function readForum($user, $forum)
    {
        $this->_em->getConnection()->executeStatement(
            "UPDATE forum_topic_user 
                SET boolRead = 1 
                WHERE idUser=:idUser
                AND idTopic IN (SELECT id FROM forum_topic WHERE idForum=:idForum)",
            ['idUser' => $user->getId(), 'idForum' => $forum->getId()]
        );
    }

    /**
     * @param $topic
     * @param $user
     */
    public function setNotRead($topic, $user)
    {
         $qb = $this->_em->createQueryBuilder();
         $query = $qb->update('ProjetNormandie\ForumBundle\Entity\TopicUser', 'tu')
            ->set('tu.boolRead', ':boolRead')
            ->where('tu.user != :user')
            ->andWhere('tu.topic = :topic')
            ->setParameter('boolRead', 0)
            ->setParameter('topic', $topic)
            ->setParameter('user', $user);

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
             ->join('tu.topic', 't')
             ->where('t.forum = :forum')
             ->andWhere('tu.user = :user')
             ->andWhere('tu.boolRead = 0')
             ->setParameter('forum', $forum)
             ->setParameter('user', $user);

        return $query->getQuery()->getSingleScalarResult();
    }
}
