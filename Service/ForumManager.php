<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\ForumUser;
use ProjetNormandie\ForumBundle\Entity\TopicUser;
use ProjetNormandie\ForumBundle\Entity\Forum;

class ForumManager
{
    private $em;

    public function __construct(EntityManagerInterface $registry)
    {
        $this->em = $registry;
    }

    /**
     * @param $user
     */
    public function initUser($user)
    {
        $list = $this->em->getRepository('ProjetNormandieForumBundle:ForumUser')->findBy(array('user' => $user));

        if (count($list) == 0) {
            $forums = $this->em->getRepository('ProjetNormandieForumBundle:Forum')->findAll();
            $topics = $this->em->getRepository('ProjetNormandieForumBundle:Topic')->findAll();

            // ForumUser
            foreach ($forums as $forum) {
                $forumUser = new ForumUser();
                $forumUser->setUser($user);
                $forumUser->setForum($forum);
                $forumUser->setBoolRead(false);
                $this->em->persist($forumUser);
            }

            // TopicUser
            foreach ($topics as $topic) {
                $topicUser = new TopicUser();
                $topicUser->setUser($user);
                $topicUser->setTopic($topic);
                $topicUser->setBoolRead(false);
                $topicUser->setBoolNotif(false);
                $this->em->persist($topicUser);
            }
            $this->em->flush();
        }
    }

    /**
     * @param array $params
     * @return Forum
     */
    public function getForum($params = array()): Forum
    {
        $forum = new Forum();
        $forum->setLibForum($params['libForum']);
        if (isset($params['libForumFr'])) {
            $forum->setLibForumFr($params['libForumFr']);
        }
        return $forum;
    }
}
