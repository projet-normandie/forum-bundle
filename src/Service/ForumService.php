<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Entity\Forum;

class ForumService
{
    private EntityManagerInterface $em;
    private TopicService $topicService;

    /**
     * ForumService constructor.
     * @param EntityManagerInterface $em
     * @param TopicService           $topicService
     */
    public function __construct(EntityManagerInterface $em, TopicService $topicService)
    {
        $this->em = $em;
        $this->topicService = $topicService;
    }

    /**
     * @param $forum
     * @return Forum
     */
    private function getForum($forum): Forum
    {
        if (!$forum instanceof Forum) {
            $forum = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\Forum')
                ->findOneBy(['id' => $forum]);
        }
        return $forum;
    }


    /**
     * @param $forum
     * @throws ORMException
     */
    public function majParent($forum)
    {
        $forum = $this->getForum($forum);
        $data = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\Forum')->getParentData($forum);
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
        $data = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\Topic')->getForumData($forum);
        $forum->setLastMessage($this->em->getReference('ProjetNormandie\ForumBundle\Entity\Message', $data['lastMessage']));
        $forum->setNbTopic($data['nbTopic']);
        $forum->setNbMessage($data['nbMessage']);
        $this->em->flush();
    }

    /**
     * @param $forum
     */
    public function majPosition($forum)
    {
        $forum = $this->getForum($forum);
        if ($forum->getIsParent()) {
            foreach ($forum->getChildrens() as $child) {
                foreach ($child->getTopics() as $topic) {
                    $this->topicService->majPositions($topic);
                }
            }
        } else {
            foreach ($forum->getTopics() as $topic) {
                $this->topicService->majPositions($topic);
            }
        }
    }


    /**
     * @param Forum $forum
     * @param       $user
     */
    public function setNotRead(Forum $forum, $user)
    {
        $forumUser = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\ForumUser')
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
        return $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\TopicUser')
                ->countNotRead($forum,$user);
    }


    /**
     * @param Forum $parent
     * @param       $user
     * @return int
     */
    public function countSubForumNotRead(Forum $parent, $user): int
    {
        return $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\ForumUser')
                ->countSubForumNotRead($parent, $user);
    }
}
