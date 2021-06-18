<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Service\TopicService;
use ProjetNormandie\ForumBundle\Service\ForumService;

class TopicListener
{
    private $majTopicUser = false;

    private $forums = array();

    private $topicService;
    private $forumService;

    /**
     * TopicListener constructor.
     * @param TopicService $topicService
     * @param ForumService $forumService
     */
    public function __construct(TopicService $topicService, ForumService $forumService)
    {
        $this->topicService = $topicService;
        $this->forumService = $forumService;
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

        if (array_key_exists('nbMessage', $changeSet)) {
            if ($changeSet['nbMessage'][0] < $changeSet['nbMessage'][1]) {
                $this->majTopicUser = true;
            }

            if ($changeSet['nbMessage'][0] != $changeSet['nbMessage'][1]) {
                $this->forums[] = $topic->getForum();
            }
        }
        if (array_key_exists('forum', $changeSet)) {
            if ($changeSet['forum'][0] != $changeSet['forum'][1]) {
                $this->forums[] = $changeSet['forum'][0];
                $this->forums[] = $changeSet['forum'][1];
            }
        }
    }


    /**
     * @param Topic              $topic
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postUpdate(Topic $topic, LifecycleEventArgs $event)
    {
        // Topic not read
        if ($this->majTopicUser) {
            $this->topicService->setNotRead($topic);
        }

        // MAJ Forum
        foreach ($this->forums as $forum) {
            $this->forumService->maj($forum);
        }
    }

     /**
     * @param Topic            $topic
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postRemove(Topic $topic,  LifecycleEventArgs $event)
    {
        // MAJ Forum
        $this->forumService->maj($topic->getForum());
    }
}
