<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Entity\Topic;

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
     * @param Topic $topic
     */
    public function majPositions(Topic $topic)
    {
        $list = $this->em->getRepository('ProjetNormandieForumBundle:Message')->findBy(['topic' => $topic], ['id' => 'ASC']);
        $i = 1;
        foreach ($list as $message) {
            $message->setPosition($i);
            $i++;
        }
        $this->em->flush();
    }

    /**
     * @param Topic $topic
     */
    public function setNotRead(Topic $topic)
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
     * @param Topic $topic
     * @throws ORMException
     */
    public function maj(Topic $topic)
    {
        $data = $this->em->getRepository('ProjetNormandieForumBundle:Message')->getTopicData($topic);
        $topic->setLastMessage($this->em->getReference('ProjetNormandie\ForumBundle\Entity\Message', $data['lastMessage']));
        $topic->setNbMessage($data['nbMessage']);
        $this->em->flush();
    }
}
