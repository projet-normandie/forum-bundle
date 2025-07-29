<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Controller;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Handler\UserDataInitHandler;
use ProjetNormandie\ForumBundle\ValueObject\ForumStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetHome extends AbstractController
{
    private UserDataInitHandler $userDataInitHandler;
    private EntityManagerInterface $em;

    public function __construct(UserDataInitHandler $userDataInitHandler, EntityManagerInterface $em)
    {
        $this->userDataInitHandler = $userDataInitHandler;
        $this->em = $em;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function __invoke(): mixed
    {
        $this->userDataInitHandler->process($this->getUser());

        $queryBuilder = $this->em->createQueryBuilder()
            ->from('ProjetNormandie\ForumBundle\Entity\Category', 'c')
            ->select('c')
            ->join('c.forums', 'f')
            ->join('f.lastMessage', 'm')
            ->join('m.user', 'u')
            ->addSelect('f')
            ->addSelect('m')
            ->addSelect('u');


        if ($this->getUser() !== null) {
            $queryBuilder
                ->join('f.forumUser', 'fu', 'WITH', 'fu.user = :user')
                ->addSelect('fu')
                ->where(
                    $queryBuilder->expr()->orX(
                        'f.status = :status1',
                        '(f.status = :status2) AND (f.role IN (:roles))'
                    )
                )
                ->setParameter('status1', ForumStatus::PUBLIC)
                ->setParameter('status2', ForumStatus::PRIVATE)
                ->setParameter('user', $this->getUser())
                ->setParameter('roles', $this->getUser() ->getRoles());
        } else {
            $queryBuilder->where('f.status = :status')
                ->setParameter('status', ForumStatus::PUBLIC);
        }

        $queryBuilder->andWhere('c.id NOT IN (8,9)');


        $queryBuilder->orderBy('c.position', 'ASC')
            ->addOrderBy('f.position', 'ASC');


        return $queryBuilder->getQuery()->getResult();
    }
}
