<?php
namespace ProjetNormandie\ForumBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ProjetNormandie\ForumBundle\Entity\Topic;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

final class TopicSubscriber implements EventSubscriberInterface
{

    private $tokenStorage;
    private $em;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setValue', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function setValue(ViewEvent $event)
    {
        $topic = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();


        if (($topic instanceof Topic) && in_array($method, array(Request::METHOD_GET))) {
            $token = $this->tokenStorage->getToken();
            if ($token->getUser() != 'anon.') {
                $userTopic = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\TopicUser')->findOneBy(
                    array(
                        'user' => $token->getUser(),
                        'topic' => $topic,
                    )
                );
                if ($userTopic) {
                    $userTopic->setBoolRead(1);
                    $this->em->flush();
                }
            }
        }
    }
}
