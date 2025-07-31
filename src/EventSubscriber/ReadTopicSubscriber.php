<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities as EventPrioritiesAlias;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use ProjetNormandie\ForumBundle\Entity\ForumUserLastVisit;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit;
use ProjetNormandie\ForumBundle\Service\TopicReadService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class ReadTopicSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private TopicReadService $topicReadService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['setRead', EventPrioritiesAlias::POST_READ],
        ];
    }

    /**
     * Marque automatiquement un topic comme lu lors de sa consultation
     */
    public function setRead(RequestEvent $event): void
    {
        $topic = $event->getRequest()->attributes->get('data');
        $method = $event->getRequest()->getMethod();
        $user = $this->security->getUser();

        if ($user && ($topic instanceof Topic) && $method == Request::METHOD_GET) {
            try {
                $this->topicReadService->markTopicAsRead($user, $topic);
            } catch (\Exception $e) {
                // Log l'erreur si nécessaire, mais ne pas interrompre la requête
            }
        }
    }
}
