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
}
