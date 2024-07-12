<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\MessageBundle\Builder\MessageBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotifyManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly MessageBuilder $messageBuilder
    ) {
    }

    /**
     * @param Message $message
     * @param string  $type
     */
    public function notify(Message $message, string $type = 'new'): void
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
