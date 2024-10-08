<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Controller;

use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Service\MarkAsReadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReadForum extends AbstractController
{
    private MarkAsReadService $markAsReadService;

    public function __construct(MarkAsReadService $markAsReadService)
    {
        $this->markAsReadService = $markAsReadService;
    }


    /**
     * @param Forum $forum
     * @return JsonResponse
     */
    public function __invoke(Forum $forum): JsonResponse
    {
        $this->markAsReadService->readForum($forum);
        return new JsonResponse(['sucess' => true]);
    }
}
