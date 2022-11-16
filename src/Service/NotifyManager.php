<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\MessageBundle\Service\Messager;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotifyManager
{
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;
    private Messager $messager;

    /**
     * MessageService constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     * @param Messager               $messager
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, Messager $messager)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->messager = $messager;
    }


    /**
     * @param Message $message
     * @param string  $type
     */
    public function notify(Message $message, string $type = 'new')
    {
        // Notify users
        $topicUsers = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\TopicUser')->findBy(
            array(
                'topic' => $message->getTopic(),
                'boolNotif' => 1
            )
        );

        foreach ($topicUsers as $topicUser) {
            $recipient = $topicUser->getUser();
            $url = '/' . $recipient->getLocale() . '/' . $message->getUrl();
            if ($topicUser->getUser()->getid() != $message->getUser()->getId()) {
                $this->messager->send(
                    sprintf(
                        $this->translator->trans(
                            'topic.notif.object.' . $type,
                            array(),
                            null,
                            $topicUser->getUser()->getLocale()
                        ),
                        $message->getTopic()->getLibTopic()
                    ),
                    sprintf(
                        $this->translator->trans(
                            'topic.notif.message',
                            array(),
                            null,
                            $topicUser->getUser()->getLocale()
                        ),
                        $message->getMessage(),
                        $url,
                        $message->getTopic()->getLibTopic()
                    ),
                    $message->getUser(),
                    $topicUser->getUser(),
                    'FORUM_NOTIF'
                );
            }
        }
    }
}
