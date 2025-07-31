<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\ForumUserLastVisit;
use ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReadAll extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Marque tous les forums et topics comme lus pour l'utilisateur connecté
     */
    public function __invoke(): JsonResponse
    {
        $user = $this->getUser();
        $now = new \DateTime();

        try {
            $this->em->beginTransaction();

            // 1. Mettre à jour toutes les visites existantes de forums
            $this->em->createQueryBuilder()
                ->update('ProjetNormandie\ForumBundle\Entity\ForumUserLastVisit', 'fuv')
                ->set('fuv.lastVisitedAt', ':now')
                ->where('fuv.user = :user')
                ->setParameter('now', $now)
                ->setParameter('user', $user)
                ->getQuery()
                ->execute();

            // 2. Créer des visites pour les forums jamais visités qui ont des messages
            $forumsNeverVisited = $this->em->createQueryBuilder()
                ->select('f')
                ->from('ProjetNormandie\ForumBundle\Entity\Forum', 'f')
                ->where('f.lastMessage IS NOT NULL')
                ->andWhere('f.id NOT IN (
                    SELECT IDENTITY(fuv.forum) 
                    FROM ProjetNormandie\ForumBundle\Entity\ForumUserLastVisit fuv 
                    WHERE fuv.user = :user
                )')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();

            foreach ($forumsNeverVisited as $forum) {
                $visit = new ForumUserLastVisit();
                $visit->setUser($user);
                $visit->setForum($forum);
                $visit->setLastVisitedAt($now);
                $this->em->persist($visit);
            }

            // 3. Mettre à jour toutes les visites existantes de topics
            $this->em->createQueryBuilder()
                ->update('ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit', 'tuv')
                ->set('tuv.lastVisitedAt', ':now')
                ->where('tuv.user = :user')
                ->setParameter('now', $now)
                ->setParameter('user', $user)
                ->getQuery()
                ->execute();

            // 4. Créer des visites pour les topics jamais visités qui ont des messages
            $topicsNeverVisited = $this->em->createQueryBuilder()
                ->select('t')
                ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
                ->where('t.lastMessage IS NOT NULL')
                ->andWhere('t.id NOT IN (
                    SELECT IDENTITY(tuv.topic) 
                    FROM ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit tuv 
                    WHERE tuv.user = :user
                )')
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
