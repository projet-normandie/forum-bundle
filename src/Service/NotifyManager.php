<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\MessageBundle\Service\MessagerBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotifyManager
{
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;
    private MessagerBuilder $messagerBuilder;

    /**
     * MessageService constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     * @param MessagerBuilder        $messagerBuilder
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, MessagerBuilder $messagerBuilder)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->messagerBuilder = $messagerBuilder;
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

        $this->messagerBuilder
            ->setType('FORUM_NOTIF')
            ->setSender($message->getUser())
        ;


        foreach ($topicUsers as $topicUser) {
            $recipient = $topicUser->getUser();
            $url = '/' . $recipient->getLocale() . '/' . $message->getUrl();
            if ($topicUser->getUser()->getid() != $message->getUser()->getId()) {
                $this->messagerBuilder
                    ->setObject(sprintf(
                        $this->translator->trans(
                            'topic.notif.object.' . $type,
                            array(),
                            null,
                            $topicUser->getUser()->getLocale()
                        ),
                        $message->getTopic()->getLibTopic()
                    ))
                    ->setMessage(sprintf(
                        $this->translator->trans(
                            'topic.notif.message',
                            array(),
                            null,
                            $topicUser->getUser()->getLocale()
                        ),
                        $message->getMessage(),
                        $url,
                        $message->getTopic()->getLibTopic()
                    ))
                ;
            }
        }
    }
}
