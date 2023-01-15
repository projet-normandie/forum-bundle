<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use ProjetNormandie\ForumBundle\Entity\Forum;

class ForumService
{
    private EntityManagerInterface $em;

    /**
     * ForumService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
     * @deprecated
     */
    /*public function majPosition($forum)
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
    }*/
}
