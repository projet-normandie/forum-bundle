<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit;
use ProjetNormandie\ForumBundle\Entity\ForumUserLastVisit;

class TopicReadService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Marque un topic comme lu pour un utilisateur
     *
     * @param mixed $user L'utilisateur
     * @param Topic $topic Le topic à marquer comme lu
     * @param bool $flush Si true, fait le flush automatiquement
     * @return array Informations sur l'opération
     */
    public function markTopicAsRead($user, Topic $topic, bool $flush = true): array
    {
        $now = new \DateTime();
        $forum = $topic->getForum();

        // 1. Vérifier si le topic est déjà lu
        $topicVisit = $this->em->getRepository(TopicUserLastVisit::class)
            ->findOneBy(['user' => $user, 'topic' => $topic]);

        $wasAlreadyRead = false;
        if ($topicVisit && $topic->getLastMessage()) {
            $wasAlreadyRead = $topicVisit->getLastVisitedAt() >= $topic->getLastMessage()->getCreatedAt();
        }

        // Si déjà lu, pas besoin de continuer
        if ($wasAlreadyRead) {
            return [
                'topicMarkedAsRead' => false,
                'forumMarkedAsRead' => false,
                'wasAlreadyRead' => true
            ];
        }

        // 2. Mettre à jour ou créer la visite du topic
        if ($topicVisit) {
            $topicVisit->setLastVisitedAt($now);
        } else {
            $topicVisit = new TopicUserLastVisit();
            $topicVisit->setUser($user);
            $topicVisit->setTopic($topic);
            $topicVisit->setLastVisitedAt($now);
            $this->em->persist($topicVisit);
        }

        // 3. Vérifier si tous les topics du forum sont maintenant lus
        $unreadTopicsCount = $this->countUnreadTopicsInForum($user, $forum);
        $forumMarkedAsRead = false;

        // 4. Si aucun topic non lu, marquer le forum comme lu
        if ($unreadTopicsCount === 0) {
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
            $forumMarkedAsRead = true;
        }

        if ($flush) {
            $this->em->flush();
        }

        return [
            'topicMarkedAsRead' => true,
            'forumMarkedAsRead' => $forumMarkedAsRead,
            'wasAlreadyRead' => false
        ];
    }

    /**
     * Compte le nombre de topics non lus dans un forum
     */
    private function countUnreadTopicsInForum($user, Forum $forum): int
    {
        try {
            // Topics visités mais avec nouveaux messages
            $visitedUnreadQuery = $this->em->createQueryBuilder()
                ->select('COUNT(t.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit', 'tuv')
                ->join('tuv.topic', 't')
                ->join('t.lastMessage', 'lm')
                ->where('t.forum = :forum')
                ->andWhere('tuv.user = :user')
                ->andWhere('lm.createdAt > tuv.lastVisitedAt')
                ->setParameter('forum', $forum)
                ->setParameter('user', $user);

            $visitedUnread = (int) $visitedUnreadQuery->getQuery()->getSingleScalarResult();

            // Topics jamais visités avec des messages
            $neverVisitedQuery = $this->em->createQueryBuilder()
                ->select('COUNT(t.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
                ->where('t.forum = :forum')
                ->andWhere('t.lastMessage IS NOT NULL')
                ->andWhere('t.id NOT IN (
                    SELECT IDENTITY(tuv2.topic) 
                    FROM ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit tuv2 
                    WHERE tuv2.user = :user
                )')
                ->setParameter('forum', $forum)
                ->setParameter('user', $user);

            $neverVisited = (int) $neverVisitedQuery->getQuery()->getSingleScalarResult();

            return $visitedUnread + $neverVisited;

        } catch (\Exception) {
            return 0;
        }
    }
}
