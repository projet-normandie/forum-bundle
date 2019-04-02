<?php

namespace ProjetNormandie\ForumBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 */
class CategoryController extends Controller
{

    /**
     * @return mixed
     */
    public function home()
    {
        $categories = $this->getDoctrine()->getRepository('ProjetNormandieForumBundle:Category')
            ->getHome()
            ->getResult();
        return $categories;
    }

}
