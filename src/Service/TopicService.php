<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Topic;

class TopicService
{
    private EntityManagerInterface $em;

    /**
     * TopicService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @param Topic $topic
     */
    public function majPositions(Topic $topic): void
    {
        $list = $this->em->getRepository('ProjetNormandie\ForumBundle\Entity\Message')
            ->findBy(['topic' => $topic], ['id' => 'ASC']);
        $i = 1;
        foreach ($list as $message) {
            $message->setPosition($i);
            $i++;
        }
        $this->em->flush();
    }
}
