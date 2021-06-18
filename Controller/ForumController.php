<?php

namespace ProjetNormandie\ForumBundle\Controller;

use ProjetNormandie\ForumBundle\Service\ForumService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class ForumController
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
    public function readAll()
    {
        $this->forumService->readAll($this->getUser());
        return new JsonResponse(['sucess' => true]);
    }
}
