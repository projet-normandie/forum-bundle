<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Controller\Forum;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Forum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GetStats extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ?CacheInterface $cache = null
    ) {
    }

    /**
     * Retourne les statistiques d'un forum spécifique
     */
    public function __invoke(Forum $forum, Request $request): JsonResponse
    {
        $extended = $request->query->getBoolean('extended', false);
        $forceRefresh = $request->query->getBoolean('refresh', false);

        if ($extended) {
            $stats = $this->getExtendedStats($forum, $forceRefresh);
        } else {
            $stats = $this->getStats($forum, $forceRefresh);
        }

        return new JsonResponse($stats);
    }

    /**
     * Récupère les statistiques du forum avec mise en cache
     */
    public function getStats(Forum $forum, bool $forceRefresh = false): array
    {
        $cacheKey = 'forum_stats_' . $forum->getId();

        if (!$this->cache || $forceRefresh) {
            return $this->calculateStats($forum);
        }

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($forum) {
            $item->expiresAfter(300); // Cache pendant 5 minutes
            return $this->calculateStats($forum);
        });
    }

    /**
     * Calcule les statistiques du forum
     */
    private function calculateStats(Forum $forum): array
    {
        $now = new \DateTime();
        $yesterday = (clone $now)->modify('-1 day');
        $today = (clone $now)->setTime(0, 0, 0);

        return [
            'nbTopic' => $this->countTopics($forum),
            'nbMessage' => $this->countMessages($forum),
            'activeUsers' => $this->countActiveUsers($forum, $yesterday),
            'todayActivity' => [
                'nbNewTopic' => $this->countNewTopicsToday($forum, $today),
                'nbNewMessage' => $this->countNewMessagesToday($forum, $today),
            ],
            'lastMessage' => $this->getLastMessageInfo($forum),
            'lastUpdate' => $now->format('c'),
        ];
    }

    /**
     * Invalide le cache des statistiques pour un forum
     */
    public function invalidateCache(Forum $forum): void
    {
        if ($this->cache) {
            $cacheKey = 'forum_stats_' . $forum->getId();
            $extendedCacheKey = 'forum_stats_extended_' . $forum->getId();

            $this->cache->delete($cacheKey);
            $this->cache->delete($extendedCacheKey);
        }
    }

    /**
     * Compte le nombre de topics non archivés dans le forum
     */
    private function countTopics(Forum $forum): int
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('COUNT(t.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
                ->where('t.forum = :forum')
                ->andWhere('t.boolArchive = :archived')
                ->setParameter('forum', $forum)
                ->setParameter('archived', false);

            return (int) $query->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Compte le nombre de messages dans le forum
     */
    private function countMessages(Forum $forum): int
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('COUNT(m.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Message', 'm')
                ->join('m.topic', 't')
                ->where('t.forum = :forum')
                ->setParameter('forum', $forum);

            return (int) $query->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Compte le nombre d'utilisateurs actifs dans le forum (dernières 24h)
     */
    private function countActiveUsers(Forum $forum, \DateTime $since): int
    {
        try {
            // Utilisateurs actifs basés sur les visites de topics du forum
            $topicVisitsQuery = $this->em->createQueryBuilder()
                ->select('DISTINCT IDENTITY(tuv.user)')
                ->from('ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit', 'tuv')
                ->join('tuv.topic', 't')
                ->where('t.forum = :forum')
                ->andWhere('tuv.lastVisitedAt >= :since')
                ->setParameter('forum', $forum)
                ->setParameter('since', $since);

            $topicActiveUsers = $topicVisitsQuery->getQuery()->getResult();

            // Utilisateurs actifs basés sur les visites du forum directement
            $forumVisitsQuery = $this->em->createQueryBuilder()
                ->select('DISTINCT IDENTITY(fuv.user)')
                ->from('ProjetNormandie\ForumBundle\Entity\ForumUserLastVisit', 'fuv')
                ->where('fuv.forum = :forum')
                ->andWhere('fuv.lastVisitedAt >= :since')
                ->setParameter('forum', $forum)
                ->setParameter('since', $since);

            $forumActiveUsers = $forumVisitsQuery->getQuery()->getResult();

            // Fusionner et dédupliquer les utilisateurs
            $allActiveUsers = array_unique(array_merge(
                array_column($topicActiveUsers, 1),
                array_column($forumActiveUsers, 1)
            ));

            return count($allActiveUsers);
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Compte le nombre de nouveaux topics créés aujourd'hui dans le forum
     */
    private function countNewTopicsToday(Forum $forum, \DateTime $today): int
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('COUNT(t.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
                ->where('t.forum = :forum')
                ->andWhere('t.createdAt >= :today')
                ->setParameter('forum', $forum)
                ->setParameter('today', $today);

            return (int) $query->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Compte le nombre de nouveaux messages créés aujourd'hui dans le forum
     */
    private function countNewMessagesToday(Forum $forum, \DateTime $today): int
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('COUNT(m.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Message', 'm')
                ->join('m.topic', 't')
                ->where('t.forum = :forum')
                ->andWhere('m.createdAt >= :today')
                ->setParameter('forum', $forum)
                ->setParameter('today', $today);

            return (int) $query->getQuery()->getSingleScalarResult();
        } catch (\Exception) {
            return 0;
        }
    }

    /**
     * Récupère les informations du dernier message du forum
     */
    private function getLastMessageInfo(Forum $forum): ?array
    {
        try {
            $message = $forum->getLastMessage();
            if (null !== $message) {
                return [
                    'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                    'username' => $message->getUser()->getUsername(),
                ];
            } else {
                return null;
            }
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Récupère des statistiques étendues pour le forum
     */
    public function getExtendedStats(Forum $forum, bool $forceRefresh = false): array
    {
        $cacheKey = 'forum_stats_extended_' . $forum->getId();

        if (!$this->cache || $forceRefresh) {
            return $this->calculateExtendedStats($forum);
        }

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($forum) {
            $item->expiresAfter(300); // Cache pendant 5 minutes
            return $this->calculateExtendedStats($forum);
        });
    }

    /**
     * Calcule les statistiques étendues pour le forum
     */
    private function calculateExtendedStats(Forum $forum): array
    {
        $baseStats = $this->calculateStats($forum);

        return array_merge($baseStats, [
            'weekActivity' => $this->getWeekActivity($forum),
            'topActiveUsers' => $this->getTopActiveUsers($forum),
            'topTopics' => $this->getTopTopics($forum),
            'recentActivity' => $this->getRecentActivity($forum),
        ]);
    }

    /**
     * Récupère l'activité de la semaine pour le forum
     */
    private function getWeekActivity(Forum $forum): array
    {
        $weekAgo = (new \DateTime())->modify('-7 days');

        try {
            $topicQuery = $this->em->createQueryBuilder()
                ->select('COUNT(t.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
                ->where('t.forum = :forum')
                ->andWhere('t.createdAt >= :weekAgo')
                ->setParameter('forum', $forum)
                ->setParameter('weekAgo', $weekAgo);

            $messageQuery = $this->em->createQueryBuilder()
                ->select('COUNT(m.id)')
                ->from('ProjetNormandie\ForumBundle\Entity\Message', 'm')
                ->join('m.topic', 't')
                ->where('t.forum = :forum')
                ->andWhere('m.createdAt >= :weekAgo')
                ->setParameter('forum', $forum)
                ->setParameter('weekAgo', $weekAgo);

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
     * Récupère les utilisateurs les plus actifs dans le forum (dernières 24h)
     */
    private function getTopActiveUsers(Forum $forum, int $limit = 5): array
    {
        $yesterday = (new \DateTime())->modify('-1 day');

        try {
            // Activité basée sur les messages postés
            $query = $this->em->createQueryBuilder()
                ->select('IDENTITY(m.user) as user_id, u.pseudo, COUNT(m.id) as activity_count')
                ->from('ProjetNormandie\ForumBundle\Entity\Message', 'm')
                ->join('m.topic', 't')
                ->join('m.user', 'u')
                ->where('t.forum = :forum')
                ->andWhere('m.createdAt >= :since')
                ->groupBy('m.user, u.pseudo')
                ->orderBy('activity_count', 'DESC')
                ->setMaxResults($limit)
                ->setParameter('forum', $forum)
                ->setParameter('since', $yesterday);

            return $query->getQuery()->getResult();
        } catch (\Exception) {
            return [];
        }
    }

    /**
     * Récupère les topics les plus actifs du forum
     */
    private function getTopTopics(Forum $forum, int $limit = 5): array
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('t.id, t.name, t.nbMessage, t.createdAt')
                ->from('ProjetNormandie\ForumBundle\Entity\Topic', 't')
                ->where('t.forum = :forum')
                ->andWhere('t.boolArchive = :archived')
                ->orderBy('t.nbMessage', 'DESC')
                ->addOrderBy('t.createdAt', 'DESC')
                ->setMaxResults($limit)
                ->setParameter('forum', $forum)
                ->setParameter('archived', false);

            return $query->getQuery()->getResult();
        } catch (\Exception) {
            return [];
        }
    }

    /**
     * Récupère l'activité récente du forum
     */
    private function getRecentActivity(Forum $forum, int $limit = 10): array
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('m.id, m.createdAt, t.id as topicId, t.name as topicName, u.pseudo')
                ->from('ProjetNormandie\ForumBundle\Entity\Message', 'm')
                ->join('m.topic', 't')
                ->join('m.user', 'u')
                ->where('t.forum = :forum')
                ->orderBy('m.createdAt', 'DESC')
                ->setMaxResults($limit)
                ->setParameter('forum', $forum);

            $results = $query->getQuery()->getResult();

            return array_map(function ($result) {
                return [
                    'messageId' => $result['id'],
                    'topicId' => $result['topicId'],
                    'topicName' => $result['topicName'],
                    'userPseudo' => $result['pseudo'],
                    'createdAt' => $result['createdAt']->format('c'),
                ];
            }, $results);
        } catch (\Exception) {
            return [];
        }
    }
}
