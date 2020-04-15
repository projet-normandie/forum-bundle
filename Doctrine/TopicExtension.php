<?php
namespace ProjetNormandie\ForumBundle\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ProjetNormandie\ForumBundle\Entity\Topic;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class TopicExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass,
        string $operationName = null
    )
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass,
        array $identifiers, string $operationName = null, array $context = []
    )
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {

        if (Topic::class !== $resourceClass || !$this->security->isGranted(
                'ROLE_USER'
            ) || null === $user = $this->security->getUser()) {
            return;
        }

        $queryBuilder->innerJoin('o.topicUser', 'tu');
        $queryBuilder->andWhere('tu.user = :current_user');
        $queryBuilder->setParameter('current_user', $user);
    }
}
