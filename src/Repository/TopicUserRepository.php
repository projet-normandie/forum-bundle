<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use ProjetNormandie\ForumBundle\Entity\TopicUser;

/**
 * Specific repository that serves the Forum entity.
 */
class TopicUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopicUser::class);
    }
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
     * @param $forum
     * @param $user
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countNotRead($forum, $user): mixed
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
