<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use ProjetNormandie\ForumBundle\Service\ForumService;
use ProjetNormandie\ForumBundle\Entity\ForumUser;

class ForumUserListener
{
    private bool $maj = false;

    private ForumService $forumService;

    /**
     * ForumListener constructor.
     * @param ForumService $forumService
     */
    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;
    }

    /**
     * @param ForumUser          $forumUser
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(ForumUser $forumUser, PreUpdateEventArgs $event): void
    {
        $changeSet = $event->getEntityChangeSet();

        if (array_key_exists('boolRead', $changeSet)) {
            if (($changeSet['boolRead'][0] != $changeSet['boolRead'][1]) && ($changeSet['boolRead'][1] == true)) {
                $this->maj = true;
            }
        }
    }


    /**
     * @param ForumUser        $forumUser
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(ForumUser $forumUser, LifecycleEventArgs $event): void
    {
        if ($this->maj && $forumUser->getForum()->getParent() != null) {
            $parent = $forumUser->getForum()->getParent();
            $user = $forumUser->getUser();
            // Count subForum read from forum
            $nb = $this->forumService->countSubForumNotRead($parent, $user);
            /*if ($nb == 0) {
                $this->forumService->setRead($parent, $user);
            }*/
        }
    }
}
