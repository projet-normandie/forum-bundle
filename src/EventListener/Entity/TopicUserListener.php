<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use ProjetNormandie\ForumBundle\Entity\TopicUser;
use ProjetNormandie\ForumBundle\Service\ForumService;

class TopicUserListener
{
    private bool $maj = false;

    private ForumService $forumService;

    /**
     * TopicUserListener constructor.
     * @param ForumService $forumService
     */
    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;
    }

    /**
     * @param TopicUser        $topicUser
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(TopicUser $topicUser, PreUpdateEventArgs $event)
    {
        $changeSet = $event->getEntityChangeSet();

        if (array_key_exists('boolRead', $changeSet)) {
            $this->maj = true;
        }
    }


    /**
     * @param TopicUser        $topicUser
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(TopicUser $topicUser, LifecycleEventArgs $event)
    {
        if ($this->maj) {
            $forum = $topicUser->getTopic()->getForum();
            $user = $topicUser->getUser();

            // IF topic not read => forum is not read
            if (false === $topicUser->getBoolRead()) {
                $this->forumService->setNotRead($forum, $user);
            } else {
                // Count topic read from forum
                $nb = $this->forumService->countTopicNotRead($forum, $user);
                if ($nb == 0) {
                    $this->forumService->setRead($forum, $user);
                }
            }
        }
    }
}
