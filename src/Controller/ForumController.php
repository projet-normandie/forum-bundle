<?php

namespace ProjetNormandie\ForumBundle\Controller;

use ProjetNormandie\ForumBundle\Service\MarkAsReadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use ProjetNormandie\ForumBundle\Entity\Forum;

/**
 * Class ForumController
 */
class ForumController extends AbstractController
{
    private MarkAsReadService $markAsReadService;

    public function __construct(MarkAsReadService $markAsReadService)
    {
        $this->markAsReadService = $markAsReadService;
    }

    /**
     * @return JsonResponse
     */
    public function readAll(): JsonResponse
    {
        $this->markAsReadService->readAll();
        return new JsonResponse(['sucess' => true]);
    }



    /**
     * @param Forum $forum
     * @return JsonResponse
     */
    public function readForum(Forum $forum): JsonResponse
    {
        $this->markAsReadService->readForum($forum);
        return new JsonResponse(['sucess' => true]);
    }
}
