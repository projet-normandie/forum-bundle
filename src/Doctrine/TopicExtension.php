<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use ProjetNormandie\ForumBundle\Entity\Topic;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bundle\SecurityBundle\Security;

final class TopicExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass, $context);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass, $context);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $resourceClass
     * @param array $context
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass, array $context): void
    {
        if (
            Topic::class !== $resourceClass || !$this->security->isGranted(
                'ROLE_USER'
            ) || null === $user = $this->security->getUser()
        ) {
            return;
        }

        if (array_key_exists('topic:topic-user-1', $context['groups'])) {
            $queryBuilder->innerJoin('o.topicUser', 'tu', Join::WITH, 'tu.user = :current_user');
            $queryBuilder->addSelect('tu');
            $queryBuilder->setParameter('current_user', $user);
        }
    }
}
