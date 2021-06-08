<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use ProjetNormandie\ForumBundle\Entity\TopicUser;
use ProjetNormandie\ForumBundle\Service\ForumUserService;

class ForumUserListener
{
    private $maj = false;

    private $forumUserService;

    /**
     * TopicUserListener constructor.
     * @param ForumUserService $forumUserService
     */
    public function __construct(ForumUserService $forumUserService,)
    {
        $this->forumUserService = $forumUserService;
    }

    /**
     * @param ForumUser          $forumUser
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(ForumUser $forumUser, PreUpdateEventArgs $event)
    {
        $changeSet = $event->getEntityChangeSet();

        if (array_key_exists('boolRead', $changeSet)) {
            if ($changeSet['boolRead'][0] != $changeSet['boolRead'][1]) {
                $this->maj = true;
            }
        }
    }


    /**
     * @param ForumUser        $forumUser
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(ForumUser $forumUser, LifecycleEventArgs $event)
    {
        if ($this->maj && $forumUser->getForum()->getParent() != null) {

        }
    }
}
