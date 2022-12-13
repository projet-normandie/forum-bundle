<?php
namespace ProjetNormandie\ForumBundle\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Forum;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Security\Core\Security;

final class ForumExtension implements QueryCollectionExtensionInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

     /**
     * @param QueryBuilder                $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string                      $resourceClass
     * @param Operation|null              $operation
     * @param array                       $context
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
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
