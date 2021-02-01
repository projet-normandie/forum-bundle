<?php
namespace ProjetNormandie\ForumBundle\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Topic;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Security\Core\Security;

final class TopicExtension implements QueryCollectionExtensionInterface
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
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string       $resourceClass
     */
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

    /**
     * @param QueryBuilder $queryBuilder
     * @param string       $resourceClass
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {

        if (Topic::class !== $resourceClass || !$this->security->isGranted(
                'ROLE_USER'
            ) || null === $user = $this->security->getUser()) {
            return;
        }


        $queryBuilder->innerJoin('o.topicUser', 'tu2', Join::WITH, 'tu2.user = :current_user2');
        $queryBuilder->addSelect('tu2');
        //$queryBuilder->andWhere('tu2.user = :current_user');
        $queryBuilder->setParameter('current_user2', $user);
    }
}
