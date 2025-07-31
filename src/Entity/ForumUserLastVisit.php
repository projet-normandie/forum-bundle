<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ProjetNormandie\ForumBundle\Repository\ForumUserLastVisitRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name:'pnf_forum_user_last_visit')]
#[ORM\Entity(repositoryClass: ForumUserLastVisitRepository::class)]
#[ORM\UniqueConstraint(name: "uniq_forum_user_visit", columns: ["user_id", "forum_id"])]
#[ORM\Index(name: "idx_user_last_visited", columns: ["user_id", "last_visited_at"])]
#[ORM\Index(name: "idx_forum_last_visited", columns: ["forum_id", "last_visited_at"])]
class ForumUserLastVisit
{
    #[Groups(['forum-user-visit:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private $user;

    #[ORM\ManyToOne(targetEntity: Forum::class)]
    #[ORM\JoinColumn(name:'forum_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Forum $forum;

    #[Groups(['forum-user-visit:read'])]
    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $lastVisitedAt;

    public function __construct()
    {
        $this->lastVisitedAt = new \DateTime();
    }

    public function __toString(): string
    {
        return sprintf(
            '%s - %s (%s)',
            $this->getUser(),
            $this->getForum(),
            $this->getLastVisitedAt()->format('Y-m-d H:i:s')
        );
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

    public function setForum(Forum $forum): void
    {
        $this->forum = $forum;
    }

    public function getForum(): Forum
    {
        return $this->forum;
    }

    public function setLastVisitedAt(\DateTime $lastVisitedAt): void
    {
        $this->lastVisitedAt = $lastVisitedAt;
    }

    public function getLastVisitedAt(): \DateTime
    {
        return $this->lastVisitedAt;
    }

    /**
     * Met à jour la date de dernière visite à maintenant
     */
    public function updateLastVisit(): void
    {
        $this->lastVisitedAt = new \DateTime();
    }

    /**
     * Vérifie si le forum a été visité après une certaine date
     */
    public function wasVisitedAfter(\DateTime $date): bool
    {
        return $this->lastVisitedAt > $date;
    }
}
