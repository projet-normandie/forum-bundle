<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ProjetNormandie\ForumBundle\Repository\ForumUserRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name:'pnf_forum_user')]
#[ORM\Entity(repositoryClass: ForumUserRepository::class)]
#[ORM\UniqueConstraint(name: "uniq_forum_user", columns: ["user_id", "forum_id"])]
class ForumUser
{
    #[Groups(['forum-user:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName:'id', nullable:false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Forum::class, inversedBy: 'forumUser')]
    #[ORM\JoinColumn(name:'forum_id', referencedColumnName:'id', nullable:false)]
    private Forum $forum;

    #[Groups(['forum-user:read'])]
    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $boolRead = false;

    public function __toString()
    {
        return sprintf('%s - %s', $this->getUser(), $this->getForum());
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
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

    public function setBoolRead(bool $boolRead): void
    {
        $this->boolRead = $boolRead;
    }

    public function getBoolRead(): bool
    {
        return $this->boolRead;
    }
}
