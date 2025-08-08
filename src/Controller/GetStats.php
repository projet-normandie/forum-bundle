<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\ValueObject\ForumStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GetStats extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ?CacheInterface $cache = null
    ) {
    }

    /**
     * Retourne les statistiques globales du forum
     */
    public function __invoke(Request $request): JsonResponse
    {
        $extended = $request->query->getBoolean('extended', false);
        $forceRefresh = $request->query->getBoolean('refresh', false);

        if ($extended) {
            $stats = $this->getExtendedStats($forceRefresh);
        } else {
            $stats = $this->getStats($forceRefresh);
        }

        return new JsonResponse($stats);
    }

    /**
     * Récupère les statistiques du forum avec mise en cache
     */
    public function getStats(bool $forceRefresh = false): array
    {
        if (!$this->cache || $forceRefresh) {
            return $this->calculateStats();
        }

        return $this->cache->get('forum_stats', function (ItemInterface $item) {
            $item->expiresAfter(300); // Cache pendant 5 minutes
            return $this->calculateStats();
        });
    }

    /**
     * Calcule les statistiques du forum
     */
    private function calculateStats(): array
    {
        $now = new \DateTime();
        $yesterday = (clone $now)->modify('-1 day');
        $today = (clone $now)->setTime(0, 0, 0);

        return [
            'nbForum' => $this->countForums(),
            'nbTopic' => $this->countTopics(),
            'nbMessage' => $this->countMessages(),
            'activeUsers' => $this->countActiveUsers($yesterday),
            'todayActivity' => [
                'nbNewTopic' => $this->countNewTopicsToday($today),
                'nbNewMessage' => $this->countNewMessagesToday($today),
            ],
            'lastUpdate' => $now->format('c'),
        ];
    }

    /**
     * Invalide le cache des statistiques
     */
    public function invalidateCache(): void
    {
        if ($this->cache) {
            $this->cache->delete('forum_stats');
            $this->cache->delete('forum_stats_extended');
        }
    }

    /**
     * Compte le nombre total de forums publics
     */
    private function countForums(): int
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('COUNT(f.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Forum', 'f')
                ->where('f.status = :status')
                ->setParameter('status', ForumStatus::PUBLIC);

            return (int) $query->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Compte le nombre total de topics non archivés dans les forums publics
     */
    private function countTopics(): int
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('COUNT(t.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
                ->join('t.forum', 'f')
                ->where('f.status = :status')
                ->andWhere('t.boolArchive = :archived')
                ->setParameter('status', ForumStatus::PUBLIC)
                ->setParameter('archived', false);

            return (int) $query->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Compte le nombre total de messages dans les forums publics
     */
    private function countMessages(): int
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('COUNT(m.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Message', 'm')
                ->join('m.topic', 't')
                ->join('t.forum', 'f')
                ->where('f.status = :status')
                ->setParameter('status', ForumStatus::PUBLIC);

            return (int) $query->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Compte le nombre d'utilisateurs actifs dans les dernières 24h
     */
    private function countActiveUsers(\DateTime $since): int
    {
        try {
            // Approche optimisée avec UNION pour éviter les doublons
            $sql = "
                SELECT COUNT(DISTINCT user_id) as count FROM (
                    SELECT user_id FROM pnf_topic_user_last_visit 
                    WHERE last_visited_at >= :since
                    UNION
                    SELECT user_id FROM pnf_forum_user_last_visit 
                    WHERE last_visited_at >= :since
                ) AS active_users
            ";

            $stmt = $this->em->getConnection()->prepare($sql);
            $result = $stmt->executeQuery(['since' => $since->format('Y-m-d H:i:s')]);

            return (int) $result->fetchOne();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Compte le nombre de nouveaux topics créés aujourd'hui
     */
    private function countNewTopicsToday(\DateTime $today): int
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('COUNT(t.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
                ->join('t.forum', 'f')
                ->where('t.createdAt >= :today')
                ->andWhere('f.status = :status')
                ->setParameter('today', $today)
                ->setParameter('status', ForumStatus::PUBLIC);

            return (int) $query->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Compte le nombre de nouveaux messages créés aujourd'hui
     */
    private function countNewMessagesToday(\DateTime $today): int
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('COUNT(m.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Message', 'm')
                ->join('m.topic', 't')
                ->join('t.forum', 'f')
                ->where('m.createdAt >= :today')
                ->andWhere('f.status = :status')
                ->setParameter('today', $today)
                ->setParameter('status', ForumStatus::PUBLIC);

            return (int) $query->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Récupère des statistiques étendues avec plus de détails
     */
    public function getExtendedStats(bool $forceRefresh = false): array
    {
        if (!$this->cache || $forceRefresh) {
            return $this->calculateExtendedStats();
        }

        return $this->cache->get('forum_stats_extended', function (ItemInterface $item) {
            $item->expiresAfter(300); // Cache pendant 5 minutes
            return $this->calculateExtendedStats();
        });
    }

    /**
     * Calcule les statistiques étendues
     */
    private function calculateExtendedStats(): array
    {
        $baseStats = $this->calculateStats();

        return array_merge($baseStats, [
            'weekActivity' => $this->getWeekActivity(),
            'topActiveUsers' => $this->getTopActiveUsers(),
            'forumBreakdown' => $this->getForumBreakdown(),
        ]);
    }

    /**
     * Récupère l'activité de la semaine
     */
    private function getWeekActivity(): array
    {
        $weekAgo = (new \DateTime())->modify('-7 days');

        try {
            $topicQuery = $this->em->createQueryBuilder()
                ->select('COUNT(t.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
                ->join('t.forum', 'f')
                ->where('t.createdAt >= :weekAgo')
                ->andWhere('f.status = :status')
                ->setParameter('weekAgo', $weekAgo)
                ->setParameter('status', ForumStatus::PUBLIC);

            $messageQuery = $this->em->createQueryBuilder()
                ->select('COUNT(m.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Message', 'm')
                ->join('m.topic', 't')
                ->join('t.forum', 'f')
                ->where('m.createdAt >= :weekAgo')
                ->andWhere('f.status = :status')
                ->setParameter('weekAgo', $weekAgo)
                ->setParameter('status', ForumStatus::PUBLIC);

            return [
                'nbNewTopicWeek' => (int) $topicQuery->getQuery()->getSingleScalarResult(),
                'nbNewMessageWeek' => (int) $messageQuery->getQuery()->getSingleScalarResult(),
            ];
        } catch (\Exception) {
            return [
                'nbNewTopicWeek' => 0,
                'nbNewMessageWeek' => 0,
            ];
        }
    }

    /**
     * Récupère les utilisateurs les plus actifs (dernières 24h)
     */
    private function getTopActiveUsers(int $limit = 5): array
    {
        $yesterday = (new \DateTime())->modify('-1 day');

        try {
            $sql = "
                SELECT user_id, COUNT(*) as activity_count FROM (
                    SELECT user_id FROM pnf_topic_user_last_visit 
                    WHERE last_visited_at >= :since
                    UNION ALL
                    SELECT user_id FROM pnf_forum_user_last_visit 
                    WHERE last_visited_at >= :since
                ) AS activities
                GROUP BY user_id
                ORDER BY activity_count DESC
                LIMIT :limit
            ";

            $stmt = $this->em->getConnection()->prepare($sql);
            $result = $stmt->executeQuery();

            return $result->fetchAllAssociative();
        } catch (\Exception) {
            return [];
        }
    }

    /**
     * Récupère une répartition par forum
     */
    private function getForumBreakdown(): array
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('f.id, f.libForum as name, f.slug, f.nbTopic, f.nbMessage')
                ->from('ProjetNormandie\ForumBundle\Entity\Forum', 'f')
                ->where('f.status = :status')
                ->orderBy('f.nbMessage', 'DESC')
                ->setMaxResults(10)
                ->setParameter('status', ForumStatus::PUBLIC);

            return $query->getQuery()->getResult();
        } catch (\Exception) {
            return [];
        }
    }
}

