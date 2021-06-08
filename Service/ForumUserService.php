<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class ForumUserService
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
     * @param $forum
     * @param $user
     */
    public function setRead($forum, $user)
    {
        $forumUser = $this->em->getRepository('ProjetNormandieForumBundle:ForumUser')
                ->findOneBy(['forum' => $forum, 'user' => $user]);
        $forumUser->setBoolRead(true);
        $this->em->flush();
    }

     /**
     * @param $forum
     * @param $user
     */
    public function setNotRead($forum, $user)
    {
        $forumUser = $this->em->getRepository('ProjetNormandieForumBundle:ForumUser')
                ->findOneBy(['forum' => $forum, 'user' => $user]);
        $forumUser->setBoolRead(false);
        $this->em->flush();
    }
}
