<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;


class MessageService
{
    private $em;

    public function __construct(EntityManagerInterface $registry)
    {
        $this->em = $registry;
    }

    /**
     *
     */
    public function majPosition()
    {
        $list = $this->em->getRepository('ProjetNormandieForumBundle:Topic')->findAll();
        foreach ($list as $topic) {
            $this->majPositionFromTopic($topic);
        }
    }

    /**
     * @param $topic
     */
    public function majPositionFromTopic($topic)
    {
        $list = $this->em->getRepository('ProjetNormandieForumBundle:Message')->findBy(['topic' => $topic], ['id' => 'ASC']);
        $i = 1;
        foreach ($list as $message) {
            $message->setPosition($i);
            $i++;
        }
        $this->em->flush();
    }
}
