<?php

namespace ProjetNormandie\ForumBundle\Controller;

use ProjetNormandie\ForumBundle\Manager\ForumManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class CategoryController
 */
class CategoryController extends AbstractController
{
    private ForumManager $forumManager;

    public function __construct(ForumManager $forumManager)
    {
        $this->forumManager = $forumManager;
    }

    /**
     * @return mixed
     */
    public function home()
    {
        if ($this->getUser() !== null) {
            $this->forumManager->initUser($this->getUser());
        }

        return $this->getDoctrine()->getRepository('ProjetNormandie\ForumBundle\Entity\Category')
            ->getHome($this->getUser())
            ->getResult();
    }
}
