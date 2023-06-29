<?php

namespace ProjetNormandie\ForumBundle\Handler;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class UserDataInitHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $user
     * @throws Exception
     */
    public function process($user): void
    {
        if (null === $user) return;

        if ($this->isInitialized($user)) return;

        try {
            $this->em->beginTransaction();

            // FORUM_USER
            $query ="INSERT INTO forum_forum_user (idForum, idUser)
                SELECT id, :idUser FROM forum_forum";
            $this->em->getConnection()->executeStatement($query, array('idUser' => $user->getId()));

            //TOPIC_USER
            $query ="INSERT INTO forum_topic_user (idTopic, idUser)
                 SELECT id, :idUser FROM forum_topic";
            $this->em->getConnection()->executeStatement($query, array('idUser' => $user->getId()));

            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * @param $user
     * @return bool
     */
    private function isInitialized($user): bool
    {
         $list = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\ForumUser')
            ->findBy(array('user' => $user));

         return count($list) > 0;
    }
}
