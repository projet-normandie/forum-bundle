<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\MessageBundle\Builder\MessageBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotifyManager
{
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;
    private MessageBuilder $messageBuilder;

    /**
     * MessageService constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     * @param MessageBuilder        $messageBuilder
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, MessageBuilder $messageBuilder)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->messageBuilder = $messageBuilder;
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
                'boolNotif' => true
            )
        );

        $this->messageBuilder
            ->setType('FORUM_NOTIF')
            ->setSender($message->getUser())
        ;


        foreach ($topicUsers as $topicUser) {
            $recipient = $topicUser->getUser();
            $url = '/' . $recipient->getLocale() . '/' . $message->getUrl();
            if ($topicUser->getUser()->getid() != $message->getUser()->getId()) {
                $this->messageBuilder
                    ->setRecipient($topicUser->getUser())
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
                $this->messageBuilder->send();
            }
        }
    }
}
