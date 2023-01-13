<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Forum;
use Symfony\Component\Security\Core\Security;

class MarkAsReadService
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
     * @return void
     */
    public function readAlL()
    {
        $user = $this->security->getUser();

        $query = $this->em->createQueryBuilder()
            ->update('ProjetNormandie\ForumBundle\Entity\ForumUser', 'fu')
            ->set('fu.boolRead', true)
            ->where('fu.user = :user')
            ->setParameter('user', $user);
        $query->getQuery()->getResult();

        $query = $this->em->createQueryBuilder()
            ->update('ProjetNormandie\ForumBundle\Entity\TopicUser', 'tu')
            ->set('tu.boolRead', true)
            ->where('tu.user = :user')
            ->setParameter('user', $user);
        $query->getQuery()->getResult();
    }

    /**
     * @param Forum $forum
     * @return void
     */
    public function readForum(Forum $forum)
    {
        $user = $this->security->getUser();

        $query = $this->em->createQueryBuilder()
            ->update('ProjetNormandie\ForumBundle\Entity\TopicUser', 'tu')
            ->set('tu.boolRead', true)
            ->where('tu.user = :user')
            ->setParameter('user', $user)
            ->andWhere('tu.topic IN (SELECT t FROM ProjetNormandie\ForumBundle\Entity\Topic t WHERE t.forum = :forum)')
            ->setParameter('forum', $forum);
        $query->getQuery()->getResult();


        $query = $this->em->createQueryBuilder()
            ->update('ProjetNormandie\ForumBundle\Entity\ForumUser', 'fu')
            ->set('fu.boolRead', true)
            ->where('fu.user = :user')
            ->setParameter('user', $user)
            ->andWhere('fu.forum = :forum')
            ->setParameter('forum', $forum);
        $query->getQuery()->getResult();

        //@todo forum parent
        if (null !== $forum->getParent()) {

        }
    }
}
