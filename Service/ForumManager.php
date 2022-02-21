<?php

namespace ProjetNormandie\ForumBundle\Service;

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
    public function initUser($user)
    {
        $list = $this->em->getRepository('ProjetNormandieForumBundle:ForumUser')->findBy(array('user' => $user));

        if (count($list) == 0) {
            $this->em->getRepository('ProjetNormandieForumBundle:ForumUser')->init($user);
            $this->em->getRepository('ProjetNormandieForumBundle:TopicUser')->init($user);
         }
    }

    /**
     * @param array $params
     * @return Forum
     * @throws ORMException
     */
    public function getForum(array $params = array()): Forum
    {
        $forum = new Forum();
        $forum->setLibForum($params['libForum']);
        if (isset($params['libForumFr'])) {
            $forum->setLibForumFr($params['libForumFr']);
        }
        if (isset($params['parent'])) {
            $forum->setParent($this->em->getReference(Forum::class, $params['parent']));
        }
        return $forum;
    }
}
