<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Service\ForumService;

class ForumListener
{
    private $forumService;

    /**
     * ForumListener constructor.
     * @param ForumService $forumService
     */
    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;
    }


    /**
     * @param Forum              $forum
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postUpdate(Forum $forum, LifecycleEventArgs $event)
    {
        if ($forum->getParent() != null) {
            $this->forumService->majParent($forum->getParent());
        }
    }
}
