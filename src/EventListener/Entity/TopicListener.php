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
    private array $changeSet = array();
    private TopicService $topicService;
    private ForumService $forumService;

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
        $this->changeSet = $event->getEntityChangeSet();
    }


    /**
     * @param Topic              $topic
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postUpdate(Topic $topic, LifecycleEventArgs $event)
    {
        // Topic not read
        if (array_key_exists('nbMessage', $this->changeSet) && array_key_exists('lastMessage', $this->changeSet)) {
            if ($this->changeSet['nbMessage'][0] < $this->changeSet['nbMessage'][1]) {
                $lastMessage = $this->changeSet['lastMessage'][1];
                $this->topicService->setNotRead($topic, $lastMessage->getUser());
            }
            if ($this->changeSet['nbMessage'][0] != $this->changeSet['nbMessage'][1]) {
                $this->forumService->maj($topic->getForum());
            }
        }

        if (array_key_exists('forum', $this->changeSet)) {
            if ($this->changeSet['forum'][0] != $this->changeSet['forum'][1]) {
                $this->forumService->maj($this->changeSet['forum'][0]);
                $this->forumService->maj($this->changeSet['forum'][1]);
            }
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
