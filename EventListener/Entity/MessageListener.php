<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\ForumBundle\Service\MessageService;
use ProjetNormandie\MessageBundle\Service\Messager;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageListener
{

    private $messager;
    private $translator;
    private $messageService;

    /**
     * MessageListener constructor.
     * @param Messager            $messager
     * @param TranslatorInterface $translator
     * @param MessageService      $messageService
     */
    public function __construct(Messager $messager, TranslatorInterface $translator, MessageService $messageService)
    {
        $this->messager = $messager;
        $this->translator = $translator;
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

        // Notify user
        $topicUsers = $em->getRepository('ProjetNormandieForumBundle:TopicUser')->findBy(
            array(
                'topic' => $message->getTopic(),
                'boolNotif' => 1
            )
        );

        foreach ($topicUsers as $topicUser) {
            if ($topicUser->getUser()->getid() != $message->getUser()->getId()) {
                $this->messager->send(
                    sprintf(
                        $this->translator->trans(
                            'topic.notif.object',
                            array(),
                            null,
                            $topicUser->getUser()->getLocale()
                        ),
                        $topic->getLibTopic()
                    ),
                    sprintf(
                        $this->translator->trans(
                            'topic.notif.message',
                            array(),
                            null,
                            $topicUser->getUser()->getLocale()
                        ),
                        $message->getMessage(),
                        $topic->getUrl(),
                        $topic->getLibTopic()
                    ),
                    $em->getReference('ProjetNormandie\ForumBundle\Entity\UserInterface', 0),
                    $topicUser->getUser(),
                    'FORUM_NOTIF'
                );
            }
        }
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
