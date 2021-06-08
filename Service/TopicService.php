<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

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
        // Topic
        $this->em->getRepository('ProjetNormandieForumBundle:TopicUser')->setNotRead($topic);
        // Forum
        $this->em->getRepository('ProjetNormandieForumBundle:ForumUser')->setNotRead($topic->getForum());
        // Forum Parent
        if ($topic->getParent() != null) {
            $this->em->getRepository('ProjetNormandieForumBundle:ForumUser')->setNotRead($topic->getForum()->getParent());
        }
    }

    /**
     * @param $topic
     * @throws ORMException
     */
    public function maj($topic)
    {
        $data = $this->em->getRepository('ProjetNormandieForumBundle:Message')->getTopicData($topic);
        $topic->setLastMessage($this->em->getReference('ProjetNormandie\ForumBundle\Entity\Message', $data['lastMessage']));
        $topic->setNbMessage($data['nbMessage']);
        $this->em->flush();
    }
}
