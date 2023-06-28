<?php

namespace ProjetNormandie\ForumBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;

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
        $list = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\ForumUser')
            ->findBy(array('user' => $user));

        if (count($list) == 0) {
            $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\ForumUser')->init($user);
            $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\TopicUser')->init($user);
        }
    }
}
