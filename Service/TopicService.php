<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Filter\Bbcode;

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
     * @return Topic
     */
    private function getTopic($topic): Topic
    {
        if (!$topic instanceof Topic) {
            $topic = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\Topic')
                ->findOneBy(['id' => $topic]);
        }
        return $topic;
    }

    /**
     * @param Topic $topic
     */
    public function majPositions(Topic $topic)
    {
        $list = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\Message')->findBy(['topic' => $topic], ['id' => 'ASC']);
        $i = 1;
        foreach ($list as $message) {
            $message->setPosition($i);
            $i++;
        }
        $this->em->flush();
    }


     /**
     * @param $topic
     */
    public function migrateBbcode($topic)
    {
        $topic = $this->getTopic($topic);
        $filter = new Bbcode();
        foreach ($topic->getMessages() as $message) {
            $message->setMessage($filter->filter($message->getMessage()));
        }
        $this->em->flush();
    }

    /**
     * @param Topic $topic
     * @param       $user
     */
    public function setNotRead(Topic $topic, $user)
    {
        //
        // Topic
        $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\TopicUser')->setNotRead($topic, $user);
        // Forum
        $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\ForumUser')->setNotRead($topic->getForum(), $user);
        // Forum Parent
        if ($topic->getForum()->getParent() != null) {
            $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\ForumUser')->setNotRead($topic->getForum()->getParent(), $user);
        }
    }

    /**
     * @param Topic $topic
     * @throws ORMException
     */
    public function maj(Topic $topic)
    {
        $data = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\Message')->getTopicData($topic);
        $topic->setLastMessage($this->em->getReference('ProjetNormandie\ForumBundle\Entity\Message', $data['lastMessage']));
        $topic->setNbMessage($data['nbMessage']);
        $this->em->flush();
    }
}
