<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\ForumUserLastVisit;

class ForumUserLastVisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumUserLastVisit::class);
    }

    /**
     * Trouve ou crée une visite pour un utilisateur et un forum
     */
    public function findOrCreateVisit($user, Forum $forum): ForumUserLastVisit
    {
        $visit = $this->findOneBy(['user' => $user, 'forum' => $forum]);

        if (!$visit) {
            $visit = new ForumUserLastVisit();
            $visit->setUser($user);
            $visit->setForum($forum);
            // La date est automatiquement définie dans le constructeur

            $this->_em->persist($visit);
        }

        return $visit;
    }

    /**
     * Met à jour la date de dernière visite pour un forum
     */
    public function updateLastVisit($user, Forum $forum, ?\DateTime $visitDate = null): void
    {
        $visit = $this->findOrCreateVisit($user, $forum);

        if ($visitDate) {
            $visit->setLastVisitedAt($visitDate);
        } else {
            $visit->updateLastVisit();
        }

        $this->_em->flush();
    }

    /**
     * Vérifie si un forum est considéré comme non lu pour un utilisateur
     */
    public function isForumUnread($user, Forum $forum): bool
    {
        $visit = $this->findOneBy(['user' => $user, 'forum' => $forum]);

        if (!$visit) {
            return true; // Jamais visité = non lu
        }

        $lastMessage = $forum->getLastMessage();
        if (!$lastMessage) {
            return false; // Pas de message = pas de nouveau contenu
        }

        return $lastMessage->getCreatedAt() > $visit->getLastVisitedAt();
    }

    /**
     * Compte le nombre de sous-forums non lus pour un forum parent
     */
    public function countUnreadSubForums($user, Forum $parent): int
    {
        $query = $this->createQueryBuilder('fuv')
            ->select('COUNT(f.id)')
            ->join('fuv.forum', 'f')
            ->join('f.lastMessage', 'lm')
            ->where('f.parent = :parent')
            ->andWhere('fuv.user = :user')
            ->andWhere('lm.createdAt > fuv.lastVisitedAt')
            ->setParameter('parent', $parent)
            ->setParameter('user', $user);

        // Ajouter les forums jamais visités
        $subQuery = $this->_em->createQueryBuilder()
            ->select('COUNT(sf.id)')
            ->from('ProjetNormandie\ForumBundle\Entity\Forum', 'sf')
            ->join('sf.lastMessage', 'slm')
            ->where('sf.parent = :parent')
            ->andWhere('sf.id NOT IN (
                SELECT IDENTITY(fuv2.forum) 
                FROM ProjetNormandie\ForumBundle\Entity\ForumUserLastVisit fuv2 
                WHERE fuv2.user = :user
            )')
            ->setParameter('parent', $parent)
            ->setParameter('user', $user);

        try {
            $visitedUnread = (int) $query->getQuery()->getSingleScalarResult();
            $neverVisited = (int) $subQuery->getQuery()->getSingleScalarResult();

            return $visitedUnread + $neverVisited;
        } catch (NoResultException | NonUniqueResultException) {
            return 0;
        }
    }

    /**
     * Marque tous les forums comme lus pour un utilisateur
     */
    public function markAllAsRead($user): void
    {
        $now = new \DateTime();

        // Mettre à jour les visites existantes
        $query = $this->_em->createQueryBuilder()
            ->update('ProjetNormandie\ForumBundle\Entity\ForumUserLastVisit', 'fuv')
            ->set('fuv.lastVisitedAt', ':now')
            ->where('fuv.user = :user')
            ->setParameter('now', $now)
            ->setParameter('user', $user);

        $query->getQuery()->execute();

        // Créer des visites pour les forums jamais visités qui ont des messages
        $forumsWithoutVisit = $this->_em->createQueryBuilder()
            ->select('f')
            ->from('ProjetNormandie\ForumBundle\Entity\Forum', 'f')
            ->where('f.id NOT IN (
                SELECT IDENTITY(fuv.forum) 
                FROM ProjetNormandie\ForumBundle\Entity\ForumUserLastVisit fuv 
                WHERE fuv.user = :user
            )')
            ->andWhere('f.lastMessage IS NOT NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        foreach ($forumsWithoutVisit as $forum) {
            $visit = new ForumUserLastVisit();
            $visit->setUser($user);
            $visit->setForum($forum);
            $visit->setLastVisitedAt($now);
            $this->_em->persist($visit);
        }

        $this->_em->flush();
    }

    /**
     * Marque un forum spécifique comme lu
     */
    public function markForumAsRead($user, Forum $forum): void
    {
        $this->updateLastVisit($user, $forum);
    }

    /**
     * Récupère tous les forums non lus pour un utilisateur
     */
    public function getUnreadForums($user): array
    {
        // Forums visités mais avec nouveaux messages
        $visitedUnread = $this->createQueryBuilder('fuv')
            ->select('f')
            ->join('fuv.forum', 'f')
            ->join('f.lastMessage', 'lm')
            ->where('fuv.user = :user')
            ->andWhere('lm.createdAt > fuv.lastVisitedAt')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        // Forums jamais visités avec des messages
        $neverVisited = $this->_em->createQueryBuilder()
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

        return array_merge($visitedUnread, $neverVisited);
    }
}
