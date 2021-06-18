<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\ForumBundle\Service\MessageService;
use ProjetNormandie\ForumBundle\Service\TopicService;

class MessageListener
{
    private $messageService;
    private $topicService;

    /**
     * MessageListener constructor.
     * @param MessageService      $messageService
     * @param TopicService        $topicService
     */
    public function __construct(MessageService $messageService, TopicService $topicService)
    {
        $this->messageService = $messageService;
        $this->topicService = $topicService;
    }


    /**
     * @param Message       $message
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Message $message, LifecycleEventArgs $event)
    {
        $message->setPosition($message->getTopic()->getNbMessage() + 1);
    }

    /**
     * @param Message            $message
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postPersist(Message $message,  LifecycleEventArgs $event)
    {
        // MAJ topic
        $this->topicService->maj($message->getTopic());
        // Notify
        $this->messageService->notify($message, 'new');
    }

    /**
     * @param Message            $message
     * @param LifecycleEventArgs $event
     * @throws ORMException
     */
    public function postUpdate(Message $message, LifecycleEventArgs $event)
    {
        // Notify
        $this->messageService->notify($message, 'edit');
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
