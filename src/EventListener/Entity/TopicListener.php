<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Entity\TopicType;
use Symfony\Bundle\SecurityBundle\Security;

class TopicListener
{
    private array $changeSet = array();
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


     /**
     * @param Topic       $topic
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Topic $topic, LifecycleEventArgs $event): void
    {
        $topic->setUser($this->security->getUser());

        // Default type topic
        $topic->setType($event->getObjectManager()->getRepository(TopicType::class)->find(3));

        foreach ($topic->getMessages() as $message) {
            $message->setUser($this->security->getUser());
        }

        $forum = $topic->getForum();
        $forum->setNbTopic($forum->getNbTopic() + 1);
    }


    /**
     * @param Topic        $topic
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Topic $topic, PreUpdateEventArgs $event): void
    {
        $this->changeSet = $event->getEntityChangeSet();
    }


    /**
     * @param Topic              $topic
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(Topic $topic, LifecycleEventArgs $event): void
    {
        // Move topic
        if (array_key_exists('forum', $this->changeSet)) {
            $nbMessage = $topic->getNbMessage();
            /** @var Forum $forumSource */
            $forumSource = $this->changeSet['forum'][0];
            /** @var Forum $forumDestination */
            $forumDestination = $this->changeSet['forum'][1];

            $forumSource->setNbTopic($forumSource->getNbTopic() - 1);
            $forumSource->setNbMessage($forumSource->getNbMessage() - $nbMessage);

            $forumDestination->setNbTopic($forumDestination->getNbTopic() + 1);
            $forumDestination->setNbMessage($forumDestination->getNbMessage() + $nbMessage);

            $event->getObjectManager()->flush();
        }
    }


    /**
     * @param Topic              $topic
     * @param LifecycleEventArgs $event
     */
    public function preRemove(Topic $topic, LifecycleEventArgs $event): void
    {
        $nbMessage = $topic->getNbMessage();

        $forum = $topic->getForum();
        $forum->setNbTopic($forum->getNbTopic() - 1);
        $forum->setNbMessage($forum->getNbMessage() - $nbMessage);
    }
}
