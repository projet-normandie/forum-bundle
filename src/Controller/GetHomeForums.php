<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Controller;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Handler\UserDataInitHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetHomeForums extends AbstractController
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

        return $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\Category')
            ->getHome($this->getUser())
            ->getResult();
    }
}
