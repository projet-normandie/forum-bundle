<?php

namespace ProjetNormandie\ForumBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Entity\Forum;

class ForumManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $registry)
    {
        $this->em = $registry;
    }

    /**
     * @param $user
     */
    public function initUser($user): void
    {
        $list = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\ForumUser')->findBy(array('user' => $user));

        if (count($list) == 0) {
            $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\ForumUser')->init($user);
            $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\TopicUser')->init($user);
         }
    }

    /**
     * @param array $params
     * @return Forum
     */
    public function getForum(array $params = array()): Forum
    {
        $forum = new Forum();
        $forum->setLibForum($params['libForum']);
        if (isset($params['libForumFr'])) {
            $forum->setLibForumFr($params['libForumFr']);
        }
        if (isset($params['parent'])) {
            $parent = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\Forum')->findOneBy(['parent' => $params]);
            $parent ?? $forum->setParent($parent);
        }
        return $forum;
    }
}
