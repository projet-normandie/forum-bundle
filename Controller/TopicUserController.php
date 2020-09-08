<?php

namespace ProjetNormandie\ForumBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Forum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\ORMException;

/**
 * Class TopicUserController
 */
class TopicUserController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $registry)
    {
        $this->em = $registry;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ORMException
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
        return new JsonResponse(['sucess' => true]);
    }
}
