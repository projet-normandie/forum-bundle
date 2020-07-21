<?php

namespace ProjetNormandie\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ProjetNormandie\ForumBundle\Service\Forum as ForumService;

/**
 * Class CategoryController
 */
class CategoryController extends AbstractController
{
    private $forumService;

    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;
    }

    /**
     * @return mixed
     */
    public function home()
    {
        if ($this->getUser() !== null) {
            $this->forumService->initUser($this->getUser());
        }

        return $this->getDoctrine()->getRepository('ProjetNormandieForumBundle:Category')
            ->getHome($this->getUser())
            ->getResult();
    }
}
