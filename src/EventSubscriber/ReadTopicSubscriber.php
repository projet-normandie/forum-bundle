<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities as EventPrioritiesAlias;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Service\MarkAsReadService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Bundle\SecurityBundle\Security;

final class ReadTopicSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly MarkAsReadService $markAsReadService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['setRead', EventPrioritiesAlias::POST_READ],
        ];
    }

    /**
     * @param RequestEvent $event
     * @return void
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function setRead(RequestEvent $event): void
    {
        $topic = $event->getRequest()->attributes->get('data');
        $method = $event->getRequest()->getMethod();
        $user = $this->security->getUser();


        if ($user && ($topic instanceof Topic) && $method == Request::METHOD_GET) {
            $this->markAsReadService->readTopic($topic);
        }
    }
}
