<?php

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use ProjetNormandie\ForumBundle\Entity\TopicType;

/**
 * Specific repository that serves the Forum entity.
 */
class TopicTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopicType::class);
    }
}
