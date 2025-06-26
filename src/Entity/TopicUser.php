<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use Doctrine\ORM\Mapping as ORM;
use ProjetNormandie\ForumBundle\Repository\TopicUserRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name:'pnf_topic_user')]
#[ORM\Entity(repositoryClass: TopicUserRepository::class)]
#[ORM\UniqueConstraint(name: "uniq_topic_user", columns: ["user_id", "topic_id"])]
#[ApiResource(
    shortName: 'ForumTopicUser',
    operations: [
        new GetCollection(),
        new Get(),
        new Put(
            security: 'is_granted("ROLE_USER") and object.getUser() == user',
            denormalizationContext: ['groups' => ['topic-user:update']],
        )
    ],
    normalizationContext: ['groups' => ['topic-read']]
)]
class TopicUser
{
    #[Groups(['topic-user:read', 'topic-user:update'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private int $id;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName:'id', nullable:false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Topic::class, inversedBy: 'topicUser')]
    #[ORM\JoinColumn(name:'topic_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Topic $topic;

    #[Groups(['topic-user:read'])]
    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $boolRead = false;

    #[Groups(['topic-user:read', 'topic-user:update'])]
    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $boolNotif = false;

    public function __toString()
    {
        return sprintf('%s - %s', $this->getUser(), $this->getTopic());
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser($user = null): void
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

    public function setBoolRead(bool $boolRead): void
    {
        $this->boolRead = $boolRead;
    }

    public function getBoolRead(): bool
    {
        return $this->boolRead;
    }

    public function setBoolNotif(bool $boolNotif): void
    {
        $this->boolNotif = $boolNotif;
    }

    public function getBoolNotif(): bool
    {
        return $this->boolNotif;
    }
}
