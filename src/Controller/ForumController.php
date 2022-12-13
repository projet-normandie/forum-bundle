<?php

namespace ProjetNormandie\ForumBundle\Controller;

use ProjetNormandie\ForumBundle\Service\ForumService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use ProjetNormandie\ForumBundle\Entity\Forum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class ForumController
 * @Route("/forum_forums")
 */
class ForumController extends AbstractController
{
    private $forumService;

    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;
    }

    /**
     * @return JsonResponse
     */
    public function readAll(): JsonResponse
    {
        $this->forumService->readAll($this->getUser());
        return new JsonResponse(['sucess' => true]);
    }

    /**
     * @param Forum $forum
     * @return JsonResponse
     */
    public function read(Forum $forum): JsonResponse
    {
        $this->forumService->read($this->getUser(), $forum);
        return new JsonResponse(['sucess' => true]);
    }
}
