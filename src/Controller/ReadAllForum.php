<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Controller;

use ProjetNormandie\ForumBundle\Service\MarkAsReadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReadAllForum extends AbstractController
{
    private MarkAsReadService $markAsReadService;

    public function __construct(MarkAsReadService $markAsReadService)
    {
        $this->markAsReadService = $markAsReadService;
    }

    /**
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        $this->markAsReadService->readAll();
        return new JsonResponse(['sucess' => true]);
    }
}
