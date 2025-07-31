<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit;

class TopicUserLastVisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopicUserLastVisit::class);
    }

    /**
     * Trouve ou crée une visite pour un utilisateur et un topic
     */
    public function findOrCreateVisit($user, Topic $topic): TopicUserLastVisit
    {
        $visit = $this->findOneBy(['user' => $user, 'topic' => $topic]);

        if (!$visit) {
            $visit = new TopicUserLastVisit();
            $visit->setUser($user);
            $visit->setTopic($topic);
            // La date est automatiquement définie dans le constructeur

            $this->_em->persist($visit);
        }

        return $visit;
    }

    /**
     * Met à jour la date de dernière visite pour un topic
     */
    public function updateLastVisit($user, Topic $topic, ?\DateTime $visitDate = null): void
    {
        $visit = $this->findOrCreateVisit($user, $topic);

        if ($visitDate) {
            $visit->setLastVisitedAt($visitDate);
        } else {
            $visit->updateLastVisit();
        }

        $this->_em->flush();
    }

    /**
     * Vérifie si un topic est lu par un utilisateur
     */
    public function isTopicRead($user, Topic $topic): bool
    {
        $visit = $this->findOneBy(['user' => $user, 'topic' => $topic]);

        if (!$visit) {
            return false; // Jamais visité = non lu
        }

        return $visit->isTopicRead();
    }

    /**
     * Compte le nombre de topics non lus dans un forum
     */
    public function countUnreadTopicsInForum($user, Forum $forum): int
    {
        // Topics visités mais avec nouveaux messages
        $visitedUnreadQuery = $this->createQueryBuilder('tuv')
            ->select('COUNT(t.id)')
            ->join('tuv.topic', 't')
            ->join('t.lastMessage', 'lm')
            ->where('t.forum = :forum')
            ->andWhere('tuv.user = :user')
            ->andWhere('lm.createdAt > tuv.lastVisitedAt')
            ->setParameter('forum', $forum)
            ->setParameter('user', $user);

        // Topics jamais visités avec des messages
        $neverVisitedQuery = $this->_em->createQueryBuilder()
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

        try {
            $visitedUnread = (int) $visitedUnreadQuery->getQuery()->getSingleScalarResult();
            $neverVisited = (int) $neverVisitedQuery->getQuery()->getSingleScalarResult();

            return $visitedUnread + $neverVisited;
        } catch (NoResultException | NonUniqueResultException) {
            return 0;
        }
    }

    /**
     * Marque tous les topics comme lus pour un utilisateur
     */
    public function markAllAsRead($user): void
    {
        $now = new \DateTime();

        // Mettre à jour les visites existantes
        $query = $this->_em->createQueryBuilder()
            ->update('ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit', 'tuv')
            ->set('tuv.lastVisitedAt', ':now')
            ->where('tuv.user = :user')
            ->setParameter('now', $now)
            ->setParameter('user', $user);

        $query->getQuery()->execute();

        // Créer des visites pour les topics jamais visités qui ont des messages
        $topicsWithoutVisit = $this->_em->createQueryBuilder()
            ->select('t')
            ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
            ->where('t.id NOT IN (
                SELECT IDENTITY(tuv.topic) 
                FROM ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit tuv 
                WHERE tuv.user = :user
            )')
            ->andWhere('t.lastMessage IS NOT NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        foreach ($topicsWithoutVisit as $topic) {
            $visit = new TopicUserLastVisit();
            $visit->setUser($user);
            $visit->setTopic($topic);
            $visit->setLastVisitedAt($now);
            $this->_em->persist($visit);
        }

        $this->_em->flush();
    }

    /**
     * Marque tous les topics d'un forum comme lus
     */
    public function markForumTopicsAsRead($user, Forum $forum): void
    {
        $now = new \DateTime();

        // Mettre à jour les visites existantes pour ce forum
        $query = $this->_em->createQueryBuilder()
            ->update('ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit', 'tuv')
            ->set('tuv.lastVisitedAt', ':now')
            ->where('tuv.user = :user')
            ->andWhere('tuv.topic IN (
                SELECT t.id FROM ProjetNormandie\ForumBundle\Entity\Topic t 
                WHERE t.forum = :forum
            )')
            ->setParameter('now', $now)
            ->setParameter('user', $user)
            ->setParameter('forum', $forum);

        $query->getQuery()->execute();

        // Créer des visites pour les topics de ce forum jamais visités
        $topicsWithoutVisit = $this->_em->createQueryBuilder()
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

        foreach ($topicsWithoutVisit as $topic) {
            $visit = new TopicUserLastVisit();
            $visit->setUser($user);
            $visit->setTopic($topic);
            $visit->setLastVisitedAt($now);
            $this->_em->persist($visit);
        }

        $this->_em->flush();
    }

    /**
     * Marque un topic spécifique comme lu
     */
    public function markTopicAsRead($user, Topic $topic): void
    {
        $this->updateLastVisit($user, $topic);
    }

    /**
     * Récupère tous les topics non lus pour un utilisateur dans un forum
     */
    public function getUnreadTopicsInForum($user, Forum $forum): array
    {
        // Topics visités mais avec nouveaux messages
        $visitedUnread = $this->createQueryBuilder('tuv')
            ->select('t')
            ->join('tuv.topic', 't')
            ->join('t.lastMessage', 'lm')
            ->where('t.forum = :forum')
            ->andWhere('tuv.user = :user')
            ->andWhere('lm.createdAt > tuv.lastVisitedAt')
            ->setParameter('forum', $forum)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        // Topics jamais visités avec des messages
        $neverVisited = $this->_em->createQueryBuilder()
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

        return array_merge($visitedUnread, $neverVisited);
    }
}
