<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use ProjetNormandie\ForumBundle\Entity\TopicUser;
use ProjetNormandie\ForumBundle\Service\ForumUserService;

class TopicUserListener
{
    private $maj = false;

    private $forumUserService;
    private $topicUserService;

    /**
     * TopicUserListener constructor.
     * @param ForumUserService $forumUserService
     * @param TopicUserService $topicUserService
     */
    public function __construct(ForumUserService $forumUserService, TopicUserService $topicUserService)
    {
        $this->forumUserService = $forumUserService;
        $this->topicUserService = $topicUserService;
    }

    /**
     * @param TopicUser        $topicUser
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(TopicUser $topicUser, PreUpdateEventArgs $event)
    {
        $changeSet = $event->getEntityChangeSet();

        if (array_key_exists('boolRead', $changeSet)) {
            if ($changeSet['boolRead'][0] != $changeSet['boolRead'][1]) {
                $this->maj = true;
            }
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
            if ($topicUser->getBoolRead() == false) {
                $this->forumUserService->setNotRead($forum, $user);
            } else {
                // Count topic read from forum
                $nb = $this->topicUserService->getNbTopicNotRead($forum, $user);
                if ($nb == 0) {
                    $this->forumUserService->setRead($forum, $user);
                }
            }
        }
    }
}
