<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

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
}
