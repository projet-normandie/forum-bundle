<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Service\TopicService;

class TopicListener
{

    private $majTopicUser = false;

    private $topicService;

    /**
     * @param TopicService      $topicService
     */
    public function __construct(TopicService $topicService)
    {
        $this->topicService = $topicService;
    }

    /**
     * @param Topic              $topic
     * @param LifecycleEventArgs $event
     */
    public function postPersist(Topic $topic, LifecycleEventArgs $event)
    {
        // Update nbTopic
        $forum = $topic->getForum();
        $forum->setNbTopic($forum->getNbTopic() + 1);

        // Parent
        $parent = $forum->getParent();
        if ($parent) {
            $parent->setNbTopic($parent->getNbTopic() + 1);
        }
    }

    /**
     * @param Topic        $topic
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Topic $topic, PreUpdateEventArgs $event)
    {
        $changeSet = $event->getEntityChangeSet();

        $this->majTopicUser = false;
        if ($changeSet['nbMessage'][0] < $changeSet['nbMessage'][1]) {
            $this->majTopicUser = true;
        }
    }


     /**
     * @param Topic            $topic
     * @param LifecycleEventArgs $event
      */
    public function postUpdate(Topic $topic, LifecycleEventArgs $event)
    {
        // Topic not read
        if ($this->majTopicUser) {
            $this->topicService->setNotRead($topic);
        }
    }
}
