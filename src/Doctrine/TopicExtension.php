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
            Topic::class !== $resourceClass ||
            !$this->security->isGranted('ROLE_USER') ||
            null === $user = $this->security->getUser()
        ) {
            return;
        }

        // Jointure LEFT avec TopicUserLastVisit pour récupérer les infos de lecture
        $queryBuilder->leftJoin(
            'o.userLastVisits',
            'tuv',
            Join::WITH,
            'tuv.user = :current_user'
        );
        $queryBuilder->addSelect('tuv');
        $queryBuilder->setParameter('current_user', $user);
    }
}
