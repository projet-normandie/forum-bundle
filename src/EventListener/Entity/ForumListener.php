<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use ProjetNormandie\ForumBundle\Entity\Forum;

class ForumListener
{
    /**
     * @param Forum $forum
     * @param LifecycleEventArgs $event
     */
    public function postPersist(Forum $forum, LifecycleEventArgs $event): void
    {
        $connection = $event->getObjectManager()->getConnection();
        $query = "INSERT INTO pnf_forum_user (forum_id, user_id, bool_read)
                 SELECT DISTINCT :idForum, user_id, 0 FROM pnf_forum_user";
        $connection->executeStatement($query, array('idForum' => $forum->getId()));
    }
}
