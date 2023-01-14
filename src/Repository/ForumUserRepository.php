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
