<?php
namespace ProjetNormandie\ForumBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ProjetNormandie\ForumBundle\Entity\Topic;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
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
     * @param GetResponseForControllerResultEvent $event
     */
    public function setValue(GetResponseForControllerResultEvent $event)
    {
        $topic = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (($topic instanceof Topic) && in_array($method, array(Request::METHOD_GET))) {
            $token = $this->tokenStorage->getToken();
            if ($token->getUser() != 'anon.') {
                $userTopic = $this->em->getRepository('ProjetNormandieForumBundle:TopicUser')->findOneBy(
                    array(
                        'user' => $token->getUser(),
                        'topic' => $topic,
                    )
                );
                $userTopic->setBoolRead(1);
                $this->em->flush();
            }
        }
    }
}