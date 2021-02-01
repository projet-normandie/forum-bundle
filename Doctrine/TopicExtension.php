<?php
namespace ProjetNormandie\ForumBundle\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Topic;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Security\Core\Security;

final class TopicExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $security;
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
        $this->addSelect($queryBuilder, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ) {
        $this->addSelect($queryBuilder, $resourceClass);
    }


    private function addSelect(QueryBuilder $queryBuilder, string $resourceClass): void
    {

        if (Topic::class !== $resourceClass
            || !$this->security->isGranted('ROLE_USER')
            || null === $user = $this->security->getUser()) {
            return;
        }

        $subQueryBuilder = $this->em->createQueryBuilder()
            ->select('tu.boolRead')
            ->from('ProjetNormandieForumBundle:TopicUser', 'tu')
            ->where('tu.user = :current_user')
            ->andWhere('tu.topic = o')
            ->setParameter('current_user', $user);


        $queryBuilder
            ->addSelect(sprintf('(%s) as %s', $subQueryBuilder->getQuery()->getDQL(), 'boolRead'))
            ->setParameter('current_user', $user);

    }
}
