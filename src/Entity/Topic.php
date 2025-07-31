<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use ProjetNormandie\ForumBundle\Controller\Topic\GetRecentActivity;
use ProjetNormandie\ForumBundle\Controller\Topic\MarkAsRead;
use ProjetNormandie\ForumBundle\Controller\Topic\ToggleNotification;
use ProjetNormandie\ForumBundle\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name:'pnf_topic')]
#[ORM\Entity(repositoryClass: TopicRepository::class)]
#[ORM\EntityListeners(["ProjetNormandie\ForumBundle\EventListener\Entity\TopicListener"])]
#[ORM\Index(name: "idx_lib_topic", columns: ["lib_topic"])]
#[ApiResource(
    shortName: 'ForumTopic',
    order: ['type.position' => 'ASC', 'lastMessage.id' => 'DESC'],
    operations: [
        new GetCollection(
            normalizationContext: ['groups' =>
                [
                    'topic:read',
                    'topic:last-message',
                    'message:read',
                    'topic:forum',
                    'forum:read',
                    'topic:type',
                    'topic-type:read'
                ]
            ]
        ),
        new GetCollection(
            uriTemplate: '/forum_topics/recent-activity',
            controller: GetRecentActivity::class,
            normalizationContext: ['groups' =>
                [
                    'topic:read',
                    'topic:last-message',
                    'message:read',
                    'topic:forum',
                    'forum:read',
                    'topic:type',
                    'topic-type:read',
                    'message:user',
                    'user:read',
                ]
            ]
        ),
        new Get(),
        new Post(
            denormalizationContext: ['groups' => ['topic:insert', 'message:insert']],
            normalizationContext: ['groups' =>
                ['topic:read', 'topic:forum', 'forum:read']
            ],
            security: 'is_granted("ROLE_USER")',
        ),
        new Put(
            security: 'is_granted("ROLE_USER") and object.getUser() == user',
        ),
        new Get(
            uriTemplate: '/forum_topics/{id}/toggle-notification',
            controller: ToggleNotification::class,
            security: 'is_granted("ROLE_USER")',
            read: false,
        ),
        new Get(
            uriTemplate: '/forum_topics/{id}/mark-as-read',
            controller: MarkAsRead::class,
            security: 'is_granted("ROLE_USER")',
            read: false,
        )
    ],
    normalizationContext: ['groups' => ['topic:read', 'topic:type', 'topic-type:read']],
)]
#[ApiResource(
    shortName: 'ForumTopic',
    uriTemplate: '/forum_forums/{id}/topics',
    uriVariables: [
        'id' => new Link(fromClass: Forum::class, toProperty: 'forum'),
    ],
    operations: [
        new GetCollection(
            order: ['type.position' => 'ASC', 'lastMessage.id' => 'DESC']
        )
    ],
    normalizationContext: ['groups' => [
        'topic:read',
        'topic:last-message',
        'message:read',
        'message:user',
        'topic:type',
        'topic-type:read']
    ],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'name' => 'partial',
        'forum' => 'exact',
        'forum.status' => 'exact',
        'topicUser.user' => 'exact',
        'topicUser.boolNotif' => 'exact'
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'lastMessage.id' => 'DESC',
    ]
)]
#[ApiFilter(BooleanFilter::class, properties: ['boolArchive'])]
class Topic
{
    use TimestampableEntity;

    #[Groups(['topic:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Groups(['topic:read', 'topic:insert'])]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(min:3, max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $name;

    #[Groups(['topic:read'])]
    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbMessage = 0;

    #[Groups(['topic:forum', 'topic:insert'])]
    #[ORM\ManyToOne(targetEntity: Forum::class, inversedBy: 'topics')]
    #[ORM\JoinColumn(name:'forum_id', referencedColumnName:'id', nullable:false)]
    private Forum $forum;

    #[Groups(['topic:user'])]
    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName:'id', nullable:false)]
    private $user;

    #[Groups(['topic:read'])]
    #[ORM\Column(length: 255)]
    #[Gedmo\Slug(fields: ['name'])]
    protected string $slug;

    #[Groups(['topic:type', 'topic:insert'])]
    #[ORM\ManyToOne(targetEntity: TopicType::class)]
    #[ORM\JoinColumn(name:'type_id', referencedColumnName:'id', nullable:false)]
    private TopicType $type;

    #[Groups(['topic:insert'])]
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'topic', cascade: ['persist'])]
    private Collection $messages;

    #[Groups(['topic:last-message'])]
    #[ORM\ManyToOne(targetEntity: Message::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name:'max_message_id', referencedColumnName:'id', nullable:true, onDelete: 'SET NULL')]
    private ?Message $lastMessage;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $boolArchive = false;

    #[ORM\OneToMany(targetEntity: TopicUserLastVisit::class, mappedBy: 'topic')]
    private Collection $userLastVisits;


    public function __toString()
    {
        return sprintf('%s [%s]', $this->getName(), $this->getId());
    }


    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->userLastVisits = new ArrayCollection();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setNbMessage(int $nbMessage): void
    {
        $this->nbMessage = $nbMessage;
    }

    public function getNbMessage(): int
    {
        return $this->nbMessage;
    }

    public function setForum(Forum $forum): void
    {
        $this->forum = $forum;
    }

    public function getForum(): Forum
    {
        return $this->forum;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setType(TopicType $type): void
    {
        $this->type = $type;
    }

    public function getType(): TopicType
    {
        return $this->type;
    }

    public function setMessages(array $messages): void
    {
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
    }

    public function addMessage(Message $message): void
    {
        $message->setTopic($this);
        $this->messages[] = $message;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function setLastMessage(?Message $message = null): void
    {
        $this->lastMessage = $message;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function setBoolArchive(bool $boolArchive): void
    {
        $this->boolArchive = $boolArchive;
    }

    public function getBoolArchive(): bool
    {
        return $this->boolArchive;
    }

    public function getLastVisitData(): ?TopicUserLastVisit
    {
        if ($this->userLastVisits->first()) {
            return $this->userLastVisits->first();
        }
        return null;
    }

    #[Groups(['topic:read-status'])]
    public function getIsRead(): ?bool
    {
        $topicVisit = $this->getLastVisitData();
        if ($topicVisit && $this->getLastMessage()) {
            return $topicVisit->getLastVisitedAt() >= $this->getLastMessage()->getCreatedAt();
        } else {
            return $this->getLastMessage() === null;
        }
    }

    #[Groups(['topic:read-status'])]
    public function hasNewContent(): ?bool
    {
        $topicVisit = $this->getLastVisitData();
        if ($topicVisit && $this->getLastMessage()) {
            return !$this->getIsRead();
        } else {
            return $this->getLastMessage() !== null;
        }
    }

    #[Groups(['topic:read-status'])]
    public function getHasBeenVisited(): ?bool
    {
        $topicVisit = $this->getLastVisitData();
        return $topicVisit !== null;
    }

    #[Groups(['topic:read-status'])]
    public function getLastVisitedAt(): ?\DateTime
    {
        $topicVisit = $this->getLastVisitData();
        return $topicVisit?->getLastVisitedAt();
    }

    #[Groups(['topic:read-status'])]
    public function getIsNotify(): ?bool
    {
        $topicVisit = $this->getLastVisitData();
        return $topicVisit && $topicVisit->getIsNotify();
    }


    public function getUrl(): string
    {
        return sprintf(
            '%s-forum-f%d/%s-topic-t%d/index',
            $this->getForum()->getSlug(),
            $this->getForum()->getId(),
            $this->getSlug(),
            $this->getId()
        );
    }
}
