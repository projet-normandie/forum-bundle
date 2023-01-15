<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use ProjetNormandie\ForumBundle\Entity\Forum;
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
     * @param       $user
     * @param Forum $parent
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countSubForumNotRead($user, Forum $parent)
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
