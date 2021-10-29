<?php
namespace ProjetNormandie\ForumBundle\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ProjetNormandie\ForumBundle\Repository\ForumRepository;
use ProjetNormandie\ForumBundle\Entity\Forum;

final class ForumCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private ForumRepository $forumRepository;

    private iterable $collectionExtensions;

    public function __construct(ForumRepository $forumRepository, iterable $collectionExtensions = [])
    {
        $this->forumRepository = $forumRepository;
        $this->collectionExtensions = $collectionExtensions;
    }

    /**
     * @param string      $resourceClass
     * @param string|null $operationName
     * @param array       $context
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Forum::class === $resourceClass;
    }

    /**
     * @param string      $resourceClass
     * @param string|null $operationName
     * @param array       $context
     * @return iterable
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $queryBuilder  = $this->forumRepository->createQueryBuilder('o');
        $queryNameGenerator = new QueryNameGenerator();

        foreach ($this->collectionExtensions as $extension) {
            /** @var ContextAwareCollectionDataProviderInterface */
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
            if ($extension instanceof ContextAwareCollectionDataProviderInterface && $extension->supportsResult($resourceClass, $operationName, $context))                 {
                return $extension->getResult($queryBuilder, $resourceClass, $operationName, $context);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
