<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use ProjetNormandie\ForumBundle\Entity\ForumUser;

/**
 * Specific repository that serves the Forum entity.
 */
class ForumUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumUser::class);
    }

    /**
     * @param $user
     * @throws Exception
     */
    public function init($user)
    {
        $query ="INSERT INTO forum_forum_user (idForum, idUser)
                SELECT id, :idUser FROM forum_forum";
        $this->_em->getConnection()->executeStatement($query, array('idUser' => $user->getId()));
    }

    /**
     * @param $user
     * @throws Exception
     */
    public function readAll($user)
    {
        $this->_em->getConnection()->executeStatement(
            "UPDATE forum_forum_user SET boolRead = 1 WHERE idUser = :idUser",
            ['idUser' => $user->getId()]
        );
    }

    /**
     * @param $forum
     * @param $user
     */
    public function setNotRead($forum, $user)
    {
         $qb = $this->_em->createQueryBuilder();
         $query = $qb->update('ProjetNormandie\ForumBundle\Entity\ForumUser', 'fu')
            ->set('fu.boolRead', ':boolRead')
            ->where('fu.user != :user')
            ->andWhere('fu.forum = :forum')
            ->setParameter('boolRead', 0)
            ->setParameter('forum', $forum)
            ->setParameter('user', $user);

        $query->getQuery()->execute();
    }

    /**
     * @param $parent
     * @param $user
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countSubForumNotRead($parent, $user)
    {
         $query = $this->createQueryBuilder('fu')
             ->select('COUNT(fu.id)')
             ->join('fu.forum', 'f')
             ->where('f.parent = :parent')
             ->andWhere('fu.user = :user')
             ->andWhere('fu.boolRead = 0')
             ->setParameter('parent', $parent)
             ->setParameter('user', $user);
        return $query->getQuery()->getSingleScalarResult();
    }
}
