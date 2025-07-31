<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Controller\Forum;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\ForumUserLastVisit;
use ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MarkAsRead extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Marque un forum spécifique et tous ses topics comme lus
     */
    public function __invoke(Forum $forum): JsonResponse
    {
        $user = $this->getUser();
        $now = new \DateTime();

        try {
            $this->em->beginTransaction();

            // 1. Mettre à jour ou créer la visite du forum
            $forumVisit = $this->em->getRepository(ForumUserLastVisit::class)
                ->findOneBy(['user' => $user, 'forum' => $forum]);

            if ($forumVisit) {
                $forumVisit->setLastVisitedAt($now);
            } else {
                $forumVisit = new ForumUserLastVisit();
                $forumVisit->setUser($user);
                $forumVisit->setForum($forum);
                $forumVisit->setLastVisitedAt($now);
                $this->em->persist($forumVisit);
            }

            // 2. Mettre à jour toutes les visites existantes de topics de ce forum
            $this->em->createQueryBuilder()
                ->update('ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit', 'tuv')
                ->set('tuv.lastVisitedAt', ':now')
                ->where('tuv.user = :user')
                ->andWhere('tuv.topic IN (
                    SELECT t.id FROM ProjetNormandie\ForumBundle\Entity\Topic t 
                    WHERE t.forum = :forum
                )')
                ->setParameter('now', $now)
                ->setParameter('user', $user)
                ->setParameter('forum', $forum)
                ->getQuery()
                ->execute();

            // 3. Créer des visites pour les topics de ce forum jamais visités qui ont des messages
            $topicsNeverVisited = $this->em->createQueryBuilder()
                ->select('t')
                ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
                ->where('t.forum = :forum')
                ->andWhere('t.lastMessage IS NOT NULL')
                ->andWhere('t.id NOT IN (
                    SELECT IDENTITY(tuv.topic) 
                    FROM ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit tuv 
                    WHERE tuv.user = :user
                )')
                ->setParameter('forum', $forum)
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();

            foreach ($topicsNeverVisited as $topic) {
                $visit = new TopicUserLastVisit();
                $visit->setUser($user);
                $visit->setTopic($topic);
                $visit->setLastVisitedAt($now);
                $this->em->persist($visit);
            }

            $this->em->flush();
            $this->em->commit();

            return new JsonResponse(['success' => true]);

        } catch (\Exception $e) {
            $this->em->rollback();
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
