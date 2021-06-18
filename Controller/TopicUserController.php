<?php

namespace ProjetNormandie\ForumBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Service\TopicService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\ORMException;

/**
 * Class TopicUserController
 */
class TopicUserController extends AbstractController
{
    private $topicService;

    public function __construct(TopicService $topicService)
    {
        $this->topicService = $topicService;
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

    /**
     * @return JsonResponse
     */
    public function readAll()
    {
        $this->topicService->readAll($this->getUser());
        return new JsonResponse(['sucess' => true]);
    }
}
