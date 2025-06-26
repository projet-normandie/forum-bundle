<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\GroupFilter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use ProjetNormandie\ForumBundle\Repository\MessageRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name:'pnf_message')]
#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\EntityListeners(["ProjetNormandie\ForumBundle\EventListener\Entity\MessageListener"])]
#[ApiResource(
    shortName: 'ForumMessage',
    order: ['id' => 'ASC'],
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['message:read', 'message:message', 'message:user', 'user:read']]
        ),
        new Get(),
        new Post(
            denormalizationContext: ['groups' => ['message:insert']],
            security: 'is_granted("ROLE_USER")',
        ),
        new Put(
            denormalizationContext: ['groups' => ['message:update']],
            security: 'is_granted("ROLE_USER") and object.getUser() == user',
        ),
    ],
    normalizationContext: ['groups' => ['message:read', 'message:message']]
)]
#[ApiResource(
    shortName: 'ForumMessage',
    uriTemplate: '/forum_topics/{id}/messages',
    uriVariables: [
        'id' => new Link(fromClass: Topic::class, toProperty: 'topic'),
    ],
    operations: [ new GetCollection() ],
    normalizationContext: ['groups' => ['message:read', 'user:read']],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'topic' => 'exact',
        'user' => 'exact',
        'topic.forum.status' => 'exact',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => 'ASC',
        'createdAt' => 'ASC',
    ]
)]
#[ApiFilter(
    GroupFilter::class,
    arguments: [
        'parameterName' => 'groups',
        'overrideDefaultGroups' => true,
        'whitelist' => [
            'message:read',
            'message:user',
            'message:topic',
            'message:message',
            'topic:read',
            'topic:forum',
            'forum:read',
            'forum:user',
            'user:read',
        ]
    ]
)]
class Message
{
    use TimestampableEntity;

    #[Groups(['message:read', 'message:update'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Groups(['message:message', 'message:insert', 'message:update'])]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'text', nullable: false)]
    private string $message;

    #[Groups(['message:read'])]
    #[ORM\Column(nullable: false, options: ['default' => 1])]
    private int $position = 1;

    #[Groups(['message:topic', 'message:insert'])]
    #[ORM\ManyToOne(targetEntity: Topic::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name:'topic_id', referencedColumnName:'id', nullable:false)]
    private Topic $topic;

    #[Groups(['message:user'])]
    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName:'id', nullable:false)]
    private $user;

    public function __toString()
    {
        return sprintf('[%s]', $this->getId());
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setTopic(Topic $topic): void
    {
        $this->topic = $topic;
    }

    public function getTopic(): Topic
    {
        return $this->topic;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getPage(): int
    {
        return (int) floor(($this->getPosition() - 1) / 20) + 1;
    }

    public function getUrl(): string
    {
        return $this->getTopic()->getUrl() . '?page=' . $this->getPage() . '#' . $this->getId();
    }
}
