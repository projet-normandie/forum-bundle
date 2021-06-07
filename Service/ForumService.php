<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

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
     * @param $forum
     */
    public function majParent($forum)
    {
        if (!$forum instanceof Forum) {
            $forum = $this->em->getRepository('ProjetNormandieForumBundle:Forum')
                ->findOneBy(['id' => $forum]);
        }
        $child = $this->em->getRepository('ProjetNormandieForumBundle:Forum')->findOneBy(['parent' => $forum], ['lastMessage' => 'DESC']);
        $forum->setLastMessage($child->getLastMessage());
        $this->em->flush();
    }
}
