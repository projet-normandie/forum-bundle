<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Entity\Chart;

/**
 * Specific repository that serves the Forum entity.
 */
class ForumUserRepository extends EntityRepository
{
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
     * @param $forum
     */
    public function setNotRead($forum)
    {
         $qb = $this->_em->createQueryBuilder();
         $query = $qb->update('ProjetNormandie\ForumBundle\Entity\ForumUser', 'fu')
            ->set('fu.boolRead', ':boolRead')
            ->where('fu.user != :user')
            ->andWhere('fu.forum = :forum')
            ->setParameter('boolRead', 0)
            ->setParameter('forum', $forum)
            ->setParameter('user', $forum->getLastMessage()->getUser());

        $query->getQuery()->execute();
    }

      /**
     * @param $parent
     * @param $user
     * @return mixed
     */
    public function getNbForumNotRead($parent, $user)
    {
         $query = $this->createQueryBuilder('fu')
             ->select('COUNT(fu.id) as nb')
             ->join('fu.forum')
             ->where('fu.parent = :parent')
             ->andWhere('fu.user = :user')
             ->andWhere('fu.boolRead = 0')
             ->setParameter('parent', $parent)
             ->setParameter('user', $user);

        return $query->getQuery()->getResult()[0]['nb'];
    }
}
