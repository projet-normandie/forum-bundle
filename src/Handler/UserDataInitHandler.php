<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Handler;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class UserDataInitHandler
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param $user
     * @throws Exception
     */
    public function process($user): void
    {
        if (null === $user) {
            return;
        }

        if ($this->isInitialized($user)) {
            return;
        }

        try {
            $this->em->beginTransaction();

            // FORUM_USER
            $query = "INSERT INTO pnf_forum_user (forum_id, user_id)
                SELECT id, :idUser FROM pnf_forum";
            $this->em->getConnection()->executeStatement($query, array('idUser' => $user->getId()));

            //TOPIC_USER
            $query = "INSERT INTO pnf_topic_user (topic_id, user_id)
                 SELECT id, :idUser FROM pnf_topic";
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
