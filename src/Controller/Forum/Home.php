<?php

namespace ProjetNormandie\ForumBundle\Controller\Forum;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Manager\ForumManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Home extends AbstractController
{
    private ForumManager $forumManager;
    private EntityManagerInterface $em;

    public function __construct(ForumManager $forumManager, EntityManagerInterface $em)
    {
        $this->forumManager = $forumManager;
        $this->em = $em;
    }

    /**
     * @return mixed
     */
    public function __invoke(): mixed
    {
        if ($this->getUser() !== null) {
            $this->forumManager->initUser($this->getUser());
        }

        return $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\Category')
            ->getHome($this->getUser())
            ->getResult();
    }
}
