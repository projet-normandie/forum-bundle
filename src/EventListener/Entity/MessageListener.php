<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\ForumBundle\Service\MarkAsNotReadService;
use ProjetNormandie\ForumBundle\Service\NotifyManager;

class MessageListener
{
    private NotifyManager $notifyManager;
    private MarkAsNotReadService $markAsNotReadService;

    /**
     * MessageListener constructor.
     * @param NotifyManager        $notifyManager
     * @param MarkAsNotReadService $markAsNotReadService
     */
    public function __construct(NotifyManager $notifyManager, MarkAsNotReadService $markAsNotReadService)
    {
        $this->notifyManager = $notifyManager;
        $this->markAsNotReadService = $markAsNotReadService;
    }


    /**
     * @param Message       $message
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Message $message, LifecycleEventArgs $event): void
    {
        $topic = $message->getTopic();
        $topic->setNbMessage($topic->getNbMessage() + 1);
        $topic->setLastMessage($message);
        $topic->setBoolArchive(false);
        $message->setPosition($topic->getNbMessage() + 1);

        $forum = $topic->getForum();
        $forum->setNbMessage($forum->getNbMessage() + 1);
        $forum->setLastMessage($message);

        $parent = $forum->getParent();
        if ($parent) {
            $parent->setNbMessage($parent->getNbMessage());
            $parent->setLastMessage($message);
        }
    }


    /**
     * @param Message            $message
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function postPersist(Message $message, LifecycleEventArgs $event): void
    {
        $this->notifyManager->notify($message);
    }

    /**
     * @param Message            $message
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(Message $message, LifecycleEventArgs $event): void
    {
        $this->notifyManager->notify($message, 'edit');
        $this->markAsNotReadService->notRead($message->getTopic());
    }
    
    /**
     * @param Message           $message
     * @param LifecycleEventArgs $event
     */
    public function preRemove(Message $message,  LifecycleEventArgs $event): void
    {
        $topic = $message->getTopic();
        $topic->setNbMessage($topic->getNbMessage() -1);

        $i = 1;
        foreach ($topic->getMessages() as $row) {
            $row->setPosition($i);
            $i++;
        }

        $forum = $topic->getForum();
        $forum->setNbMessage($forum->getNbMessage() - 1);

        $parent = $forum->getParent();
        $parent?->setNbMessage($parent->getNbMessage() - 1);
    }

    /**
     * @param Message            $message
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function postRemove(Message $message, LifecycleEventArgs $event): void
    {
        $topic = $message->getTopic();
        $forum = $topic->getForum();
        $parent = $forum->getParent();
        $lastMessage = $topic->getMessages()->last();
        if ($message === $topic->getLastMessage()) {
            $topic->setLastMessage($lastMessage);
            $event->getObjectManager()->flush();
        }
        if ($message === $forum->getLastMessage()) {
            $forum->setLastMessage($lastMessage);
            $event->getObjectManager()->flush();
        }
        if ($parent && $message === $parent->getLastMessage()) {
            $parent->setLastMessage($lastMessage);
        }
    }
}
