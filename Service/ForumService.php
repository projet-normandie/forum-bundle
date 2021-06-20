<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Entity\Forum;

class ForumService
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
     * @param $user
     */
    public function readAll($user)
    {
        $this->em->getRepository('ProjetNormandieForumBundle:TopicUser')->readAll($user);
        $this->em->getRepository('ProjetNormandieForumBundle:ForumUser')->readAll($user);
    }

    /**
     * @param $user
     * @param $forum
     */
    public function read($user, $forum)
    {
        $this->em->getRepository('ProjetNormandieForumBundle:TopicUser')->readForum($user, $forum);
        $this->setRead($forum, $user);
    }

    /**
     * @param $forum
     * @throws ORMException
     */
    public function majParent($forum)
    {
        if (!$forum instanceof Forum) {
            $forum = $this->em->getRepository('ProjetNormandieForumBundle:Forum')
                ->findOneBy(['id' => $forum]);
        }

        $data = $this->em->getRepository('ProjetNormandieForumBundle:Forum')->getParentData($forum);
        $forum->setLastMessage($this->em->getReference('ProjetNormandie\ForumBundle\Entity\Message', $data['lastMessage']));
        $forum->setNbTopic($data['nbTopic']);
        $forum->setNbMessage($data['nbMessage']);
        $this->em->flush();
    }


    /**
     * @param Forum $forum
     * @throws ORMException
     */
    public function maj(Forum $forum)
    {
        $data = $this->em->getRepository('ProjetNormandieForumBundle:Topic')->getForumData($forum);
        $forum->setLastMessage($this->em->getReference('ProjetNormandie\ForumBundle\Entity\Message', $data['lastMessage']));
        $forum->setNbTopic($data['nbTopic']);
        $forum->setNbMessage($data['nbMessage']);
        $this->em->flush();
    }


    /**
     * @param Forum $forum
     * @param       $user
     */
    public function setRead(Forum $forum, $user)
    {
        $forumUser = $this->em->getRepository('ProjetNormandieForumBundle:ForumUser')
                ->findOneBy(['forum' => $forum, 'user' => $user]);
        $forumUser->setBoolRead(true);
        $this->em->flush();
    }


    /**
     * @param Forum $forum
     * @param       $user
     */
    public function setNotRead(Forum $forum, $user)
    {
        $forumUser = $this->em->getRepository('ProjetNormandieForumBundle:ForumUser')
                ->findOneBy(['forum' => $forum, 'user' => $user]);
        $forumUser->setBoolRead(false);
        $this->em->flush();
    }


    /**
     * @param Forum $forum
     * @param       $user
     * @return int
     */
    public function countTopicNotRead(Forum $forum, $user): int
    {
        return $this->em->getRepository('ProjetNormandieForumBundle:TopicUser')
                ->countNotRead($forum,$user);
    }


    /**
     * @param Forum $parent
     * @param       $user
     * @return int
     */
    public function countSubForumNotRead(Forum $parent, $user): int
    {
        return $this->em->getRepository('ProjetNormandieForumBundle:ForumUser')
                ->countSubForumNotRead($parent, $user);
    }
}
