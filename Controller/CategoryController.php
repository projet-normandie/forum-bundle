<?php

namespace ProjetNormandie\ForumBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Class CategoryController
 */
class CategoryController extends Controller
{

    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return mixed
     */
    public function home()
    {
        $categories = $this->getDoctrine()->getRepository('ProjetNormandieForumBundle:Category')
            ->getHome($this->getUser())
            ->getResult();
        return $categories;
    }
}
