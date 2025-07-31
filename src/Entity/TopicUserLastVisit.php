<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ProjetNormandie\ForumBundle\Repository\TopicUserLastVisitRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name:'pnf_topic_user_last_visit')]
#[ORM\Entity(repositoryClass: TopicUserLastVisitRepository::class)]
#[ORM\UniqueConstraint(name: "uniq_topic_user_visit", columns: ["user_id", "topic_id"])]
#[ORM\Index(name: "idx_user_last_visited", columns: ["user_id", "last_visited_at"])]
#[ORM\Index(name: "idx_topic_last_visited", columns: ["topic_id", "last_visited_at"])]
class TopicUserLastVisit
{
    #[Groups(['topic-user-visit:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private $user;

    #[ORM\ManyToOne(targetEntity: Topic::class)]
    #[ORM\JoinColumn(name:'topic_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Topic $topic;

    #[Groups(['topic-user-visit:read'])]
    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $lastVisitedAt;

    #[Groups(['topic-user-visit:read'])]
    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $isNotify = false;

    public function __construct()
    {
        $this->lastVisitedAt = new \DateTime();
    }

    public function __toString(): string
    {
        return sprintf('%s - %s (%s)', $this->getUser(), $this->getTopic(), $this->getLastVisitedAt()->format('Y-m-d H:i:s'));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setTopic(Topic $topic): void
    {
        $this->topic = $topic;
    }

    public function getTopic(): Topic
    {
        return $this->topic;
    }

    public function setLastVisitedAt(\DateTime $lastVisitedAt): void
    {
        $this->lastVisitedAt = $lastVisitedAt;
    }

    public function getLastVisitedAt(): \DateTime
    {
        return $this->lastVisitedAt;
    }

    public function setIsNotify(bool $isNotify): void
    {
        $this->isNotify = $isNotify;
    }

    public function getIsNotify(): bool
    {
        return $this->isNotify;
    }

    /**
     * Met à jour la date de dernière visite à maintenant
     */
    public function updateLastVisit(): void
    {
        $this->lastVisitedAt = new \DateTime();
    }

    /**
     * Vérifie si le topic a été visité après une certaine date
     */
    public function wasVisitedAfter(\DateTime $date): bool
    {
        return $this->lastVisitedAt > $date;
    }

    /**
     * Vérifie si le topic est considéré comme lu par rapport au dernier message
     */
    public function isTopicRead(): bool
    {
        $lastMessage = $this->topic->getLastMessage();

        if (!$lastMessage) {
            return true; // Pas de message = considéré comme lu
        }

        return $this->lastVisitedAt >= $lastMessage->getCreatedAt();
    }
}
