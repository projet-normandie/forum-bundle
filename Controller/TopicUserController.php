<?php

namespace ProjetNormandie\ForumBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Forum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Class TopicUserController
 */
class TopicUserController extends Controller
{

    private $em;
    private $userManager;

    public function __construct(UserManagerInterface $userManager, EntityManagerInterface $registry)
    {
        $this->userManager = $userManager;
        $this->em = $registry;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function setRead(Request $request)
    {
        $idForum = $request->query->get('idForum', null);
        if ($idForum === null) {
            $this->getDoctrine()->getRepository('ProjetNormandieForumBundle:TopicUser')->read($this->getUser());
        } else {
            $this->getDoctrine()->getRepository('ProjetNormandieForumBundle:TopicUser')->read(
                $this->getUser(),
                $this->em->getReference(Forum::class, $idForum)
            );
        }
        return new JsonResponse(['data' => true]);
    }
}
