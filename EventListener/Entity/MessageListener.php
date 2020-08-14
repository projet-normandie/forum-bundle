<?php

namespace ProjetNormandie\ForumBundle\EventListener\Entity;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\MessageBundle\Service\Messager;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageListener
{

    private $messager;
    private $translator;

    /**
     * MessageListener constructor.
     * @param Messager $messager
     * @param TranslatorInterface     $translator
     */
    public function __construct(Messager $messager, TranslatorInterface $translator)
    {
        $this->messager = $messager;
        $this->translator = $translator;
    }

    /**
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();
        if (!$entity instanceof Message) {
            return;
        }

        $topic = $entity->getTopic();

        // Notify user
        $em = $event->getEntityManager();
        $topicUsers = $em->getRepository('ProjetNormandieForumBundle:TopicUser')->findBy(
            array(
                'topic' => $entity->getTopic(),
                'boolNotif' => 1
            )
        );

        foreach ($topicUsers as $topicUser) {
            if ($topicUser->getUser()->getid() != $entity->getUser()->getId()) {
                $this->messager->send(
                    sprintf(
                        $this->translator->trans(
                            'topic.notif.object', array(), null, $topicUser->getUser()
                            ->getLocale()
                        ), $topic->getLibTopic()
                    ), sprintf(
                        $this->translator->trans(
                            'topic.notif.message', array(), null, $topicUser->getUser()
                            ->getLocale()
                        ), $entity->getMessage(), $topic->getUrl(), $topic->getLibTopic()
                    ), $em->getReference('ProjetNormandie\ForumBundle\Entity\UserInterface', 0), $topicUser->getUser(),
                    'FORUM_NOTIF'
                );
            }
        }
    }
}
