<?php
namespace ProjetNormandie\ForumBundle\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities as EventPrioritiesAlias;
use ProjetNormandie\ForumBundle\Entity\Topic;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

final class ReadTopicSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private EntityManagerInterface $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['setRead', EventPrioritiesAlias::POST_READ],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function setRead(RequestEvent $event)
    {
        $topic = $event->getRequest()->attributes->get('data');
        $method = $event->getRequest()->getMethod();
        $user = $this->security->getUser();

        if ($user && ($topic instanceof Topic) && $method == Request::METHOD_GET) {
            $userTopic = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\TopicUser')->findOneBy(
                array(
                    'user' => $user,
                    'topic' => $topic,
                )
            );
            if ($userTopic) {
                $userTopic->setBoolRead(true);
                $this->em->flush();
            }
        }
    }
}
