<?php
namespace ProjetNormandie\ForumBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Repository\ForumRepository;

final class ForumItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private ForumRepository $forumRepository;

    private iterable $itemExtensions;

    public function __construct(ForumRepository $forumRepository, iterable $itemExtensions = [])
    {
        $this->forumRepository = $forumRepository;
        $this->itemExtensions = $itemExtensions;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Forum::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Forum
    {
        return $this->forumRepository->findOneBy(['id' => $id]);
    }
}