<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\ForumBundle\Service\MessageService;

class MessageListener
{
    private $messageService;

    /**
     * MessageListener constructor.
     * @param MessageService      $messageService
     */
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
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
        $em = $event->getEntityManager();

        // Update nbMessage & lastMessage
        $topic = $message->getTopic();
        $topic->setLastMessage($message);
        $topic->setNbMessage($topic->getNbMessage() + 1);

        $forum = $topic->getForum();
        $forum->setLastMessage($message);
        $forum->setNbMessage($forum->getNbMessage() + 1);

        $em->flush();

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
     */
    public function postRemove(Message $message,  LifecycleEventArgs $event)
    {
        // MAJ position
        $this->messageService->majPositionFromTopic($message->getTopic());
    }
}
