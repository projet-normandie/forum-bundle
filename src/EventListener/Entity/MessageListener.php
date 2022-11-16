<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\ForumBundle\Service\NotifyManager;
use ProjetNormandie\ForumBundle\Service\TopicService;

class MessageListener
{
    private NotifyManager $notifyManager;
    private TopicService $topicService;

    /**
     * MessageListener constructor.
     * @param NotifyManager      $notifyManager
     * @param TopicService        $topicService
     */
    public function __construct(NotifyManager $notifyManager, TopicService $topicService)
    {
        $this->notifyManager = $notifyManager;
        $this->topicService = $topicService;
    }


    /**
     * @param Message       $message
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Message $message, LifecycleEventArgs $event)
    {
        $message->getTopic()->setBoolArchive(false);
        $message->setPosition($message->getTopic()->getNbMessage() + 1);
    }

    /**
     * @param Message            $message
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postPersist(Message $message,  LifecycleEventArgs $event)
    {
        // MAJ topic
        $this->topicService->maj($message->getTopic());
        // Notify
        $this->notifyManager->notify($message);
    }

    /**
     * @param Message            $message
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(Message $message, LifecycleEventArgs $event)
    {
        // Notify
        $this->notifyManager->notify($message, 'edit');
    }

    /**
     * @param Message            $message
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postRemove(Message $message,  LifecycleEventArgs $event)
    {
        // MAJ topic
        $this->topicService->maj($message->getTopic());
        // MAJ position
        $this->topicService->majPositions($message->getTopic());
    }
}
