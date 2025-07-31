<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Controller;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\ValueObject\ForumStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetHome extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function __invoke(): mixed
    {
        $user = $this->getUser();

        $queryBuilder = $this->em->createQueryBuilder()
            ->from('ProjetNormandie\ForumBundle\Entity\Category', 'c')
            ->select('c')
            ->join('c.forums', 'f')
            ->join('f.lastMessage', 'm')
            ->join('m.user', 'u')
            ->addSelect('f')
            ->addSelect('m')
            ->addSelect('u');

        if ($user !== null) {
            // Jointure LEFT pour les visites de forum (optionnelle)
            $queryBuilder
                ->leftJoin(
                    'f.userLastVisits',
                    'fuv',
                    'WITH',
                    'fuv.user = :user'
                )
                ->addSelect('fuv')
                ->where(
                    $queryBuilder->expr()->orX(
                        'f.status = :status1',
                        '(f.status = :status2) AND (f.role IN (:roles))'
                    )
                )
                ->setParameter('status1', ForumStatus::PUBLIC)
                ->setParameter('status2', ForumStatus::PRIVATE)
                ->setParameter('user', $user)
                ->setParameter('roles', $user->getRoles());
        } else {
            $queryBuilder->where('f.status = :status')
                ->setParameter('status', ForumStatus::PUBLIC);
        }

        // Filtrer les catégories qui doivent être affichées sur la home
        $queryBuilder->andWhere('c.displayOnHome = :displayOnHome')
            ->setParameter('displayOnHome', true);

        $queryBuilder->orderBy('c.position', 'ASC')
            ->addOrderBy('f.position', 'ASC');

        $categories = $queryBuilder->getQuery()->getResult();

        // Si utilisateur connecté, enrichir avec les compteurs de topics non lus
        if ($user !== null) {
            $this->enrichWithReadStatus($categories, $user);
        }

        return $categories;
    }

    /**
     * Enrichit les catégories avec les informations de lecture
     */
    private function enrichWithReadStatus(array $categories, $user): void
    {
        // Récupérer tous les forums des catégories
        $forumIds = [];
        $forumsById = [];

        foreach ($categories as $category) {
            foreach ($category->getForums() as $forum) {
                $forumIds[] = $forum->getId();
                $forumsById[$forum->getId()] = $forum;
            }
        }

        if (empty($forumIds)) {
            return;
        }

        // Récupérer les compteurs de topics non lus pour tous les forums
        $unreadCounts = $this->getUnreadTopicsCountByForum($user, $forumIds);

        // Enrichir chaque forum avec ses informations
        foreach ($forumsById as $forumId => $forum) {
            // Compter les topics non lus
            $forum->unreadTopicsCount = $unreadCounts[$forumId] ?? 0;
            $forum->isUnread = $forum->unreadTopicsCount > 0;
        }
    }


    /**
     * Récupère le nombre de topics non lus par forum en une seule requête optimisée
     */
    private function getUnreadTopicsCountByForum($user, array $forumIds): array
    {
        // Requête 1: Topics visités mais avec nouveaux messages
        $visitedUnreadQuery = $this->em->createQueryBuilder()
            ->select('IDENTITY(t.forum) as forum_id, COUNT(t.id) as unread_count')
            ->from('ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit', 'tuv')
            ->join('tuv.topic', 't')
            ->join('t.lastMessage', 'lm')
            ->where('t.forum IN (:forumIds)')
            ->andWhere('tuv.user = :user')
            ->andWhere('lm.createdAt > tuv.lastVisitedAt')
            ->groupBy('t.forum')
            ->setParameter('forumIds', $forumIds)
            ->setParameter('user', $user);

        $visitedUnread = $visitedUnreadQuery->getQuery()->getResult();

        // Requête 2: Topics jamais visités avec des messages
        $neverVisitedQuery = $this->em->createQueryBuilder()
            ->select('IDENTITY(t.forum) as forum_id, COUNT(t.id) as unread_count')
            ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
            ->where('t.forum IN (:forumIds)')
            ->andWhere('t.lastMessage IS NOT NULL')
            ->andWhere('t.id NOT IN (
                SELECT IDENTITY(tuv2.topic) 
                FROM ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit tuv2 
                WHERE tuv2.user = :user
            )')
            ->groupBy('t.forum')
            ->setParameter('forumIds', $forumIds)
            ->setParameter('user', $user);

        $neverVisited = $neverVisitedQuery->getQuery()->getResult();

        // Fusionner les résultats
        $result = [];

        // Initialiser tous les forums à 0
        foreach ($forumIds as $forumId) {
            $result[$forumId] = 0;
        }

        // Ajouter les topics visités mais non lus
        foreach ($visitedUnread as $row) {
            $result[(int)$row['forum_id']] += (int)$row['unread_count'];
        }

        // Ajouter les topics jamais visités
        foreach ($neverVisited as $row) {
            $result[(int)$row['forum_id']] += (int)$row['unread_count'];
        }

        return $result;
    }
}
