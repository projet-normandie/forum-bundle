<?php

namespace ProjetNormandie\ForumBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use ProjetNormandie\ForumBundle\Service\Forum as ForumService;

/**
 * Class CategoryController
 */
class CategoryController extends Controller
{

    private $userManager;
    private $forumService;

    public function __construct(UserManagerInterface $userManager, ForumService $forumService)
    {
        $this->userManager = $userManager;
        $this->forumService = $forumService;
    }

    /**
     * @return mixed
     */
    public function home()
    {
        if ($this->getUser() != null) {
            $this->forumService->initUser($this->getUser());
        }

        $categories = $this->getDoctrine()->getRepository('ProjetNormandieForumBundle:Category')
            ->getHome($this->getUser())
            ->getResult();
        return $categories;
    }
}
