<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ProjetNormandie\ForumBundle\Entity\Topic;

class TopicListener
{

    /**
     * TopicListener constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param Topic              $topic
     * @param LifecycleEventArgs $event
     */
    public function postPersist(Topic $topic, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();

        // Update nbTopic
        $forum = $topic->getForum();
        $forum->setNbTopic($forum->getNbTopic() + 1);

        // Parent
        $parent = $forum->getParent();
        if ($parent) {
            $parent->setNbTopic($parent->getNbTopic() + 1);
        }
    }
}
