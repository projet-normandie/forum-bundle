<?php
namespace ProjetNormandie\ForumBundle\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Topic;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Security\Core\Security;

final class ForumExtension implements QueryCollectionExtensionInterface
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
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string       $resourceClass
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {

        if (Forum::class !== $resourceClass || !$this->security->isGranted(
                'ROLE_USER'
            ) || null === $user = $this->security->getUser()) {
            return;
        }

        $queryBuilder->innerJoin('o.forumUser', 'fu', Join::WITH, 'fu.user = :current_user');
        $queryBuilder->addSelect('fu');
        $queryBuilder->setParameter('current_user', $user);
    }
}