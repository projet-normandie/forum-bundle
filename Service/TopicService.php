<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class TopicService
{
    private $em;

    /**
     * MessageService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $topic
     */
    public function setNotRead($topic)
    {
         $this->em->getRepository('ProjetNormandieForumBundle:TopicUser')->setNotRead($topic);
    }
}
