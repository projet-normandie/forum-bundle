<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Entity\Forum;
use Symfony\Component\Security\Core\Security;

class MarkAsNotReadService
{
    private Security $security;
    private EntityManagerInterface $em;

    /**
     * @param Security      $security
     * @param EntityManagerInterface $em
     */
    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    /**
     * @param Topic $topic
     */
    public function notRead(Topic $topic): void
    {
        // Topic
        $this->setTopicNotRead($topic);

        // Forum
        $forum = $topic->getForum();
        $this->setForumNotRead($forum);
        if ($forum->getParent()) {
            $this->setForumNotRead($forum->getParent());
        }
    }

    /**
     * @param Topic $topic
     */
    private function setTopicNotRead(Topic $topic): void
    {
        $user = $this->security->getUser();
        $qb = $this->em->createQueryBuilder();
        $query = $qb->update('ProjetNormandie\ForumBundle\Entity\TopicUser', 'tu')
            ->set('tu.boolRead', ':boolRead')
            ->where('tu.user != :user')
            ->andWhere('tu.topic = :topic')
            ->setParameter('boolRead', false)
            ->setParameter('topic', $topic)
            ->setParameter('user', $user);

        $query->getQuery()->execute();
    }

    /**
     * @param Forum $forum
     */
    private function setForumNotRead(Forum $forum): void
    {
         $user = $this->security->getUser();
         $qb = $this->em->createQueryBuilder();
         $query = $qb->update('ProjetNormandie\ForumBundle\Entity\ForumUser', 'fu')
            ->set('fu.boolRead', ':boolRead')
            ->where('fu.user != :user')
            ->andWhere('fu.forum = :forum')
            ->setParameter('boolRead', false)
            ->setParameter('forum', $forum)
            ->setParameter('user', $user);

        $query->getQuery()->execute();
    }
}
