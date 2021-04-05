<?php

namespace ProjetNormandie\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ProjetNormandie\ForumBundle\Service\ForumManager;

/**
 * Class CategoryController
 */
class CategoryController extends AbstractController
{
    private $forumManager;

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

        return $this->getDoctrine()->getRepository('ProjetNormandieForumBundle:Category')
            ->getHome($this->getUser())
            ->getResult();
    }
}
