<?php
namespace ProjetNormandie\ForumBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ProjetNormandie\ForumBundle\Entity\Message;
use ProjetNormandie\ForumBundle\Entity\Topic;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class TokenSubscriber implements EventSubscriberInterface
{

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setUser', EventPriorities::PRE_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function setUser(ViewEvent $event)
    {
        $object = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        // POST MESSAGE
        if (($object instanceof Message) && in_array($method, array(Request::METHOD_POST))) {
            $object->setUser($this->tokenStorage->getToken()->getUser());
        }

        // POST TOPIC
        if (($object instanceof Topic) && in_array($method, array(Request::METHOD_POST))) {
            $object->setUser($this->tokenStorage->getToken()->getUser());
            foreach ($object->getMessages() as $message) {
                $message->setUser($this->tokenStorage->getToken()->getUser());
            }
        }
    }
}
