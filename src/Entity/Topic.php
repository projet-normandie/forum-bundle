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
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\GroupFilter;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ProjetNormandie\ForumBundle\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
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
                ['topic:read', 'topic:last-message', 'message:read', 'topic:forum', 'forum:read', 'topic:type', 'topic-type:read']
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
    operations: [ new GetCollection() ],
    normalizationContext: ['groups' => ['forum:read']],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'libTopic' => 'partial',
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
#[ApiFilter(
    GroupFilter::class,
    arguments: [
        'parameterName' => 'groups',
        'overrideDefaultGroups' => true,
        'whitelist' => [
            'forum:read',
            'topic:read',
            'topic:type',
            'topic-type:read',
            'topic:forum',
            'topic:user',
            'forum:read"',
            'topic:last-message',
            'topic-user:read',
            'topic:topic-user-1',
            'message:read',
            'message:user',
            'user:read'
        ]
    ]
)]
#[ApiFilter(BooleanFilter::class, properties: ['boolArchive'])]
class Topic implements TimestampableInterface, SluggableInterface
{
    use TimestampableTrait;
    use SluggableTrait;

    #[Groups(['topic:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Groups(['topic:read', 'topic:insert'])]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(min:3, max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $libTopic;

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

    #[ORM\OneToMany(targetEntity: TopicUser::class, mappedBy: 'topic')]
    private Collection $topicUser;


    public function __toString()
    {
        return sprintf('%s [%s]', $this->getLibTopic(), $this->getId());
    }


    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->topicUser = new ArrayCollection();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLibTopic(string $libTopic): void
    {
        $this->libTopic = $libTopic;
    }

    public function getLibTopic(): string
    {
        return $this->libTopic;
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

    public function setLastMessage(Message $message = null): void
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

    public function getTopicUser(): Collection
    {
        return $this->topicUser;
    }

    #[Groups(['topic:topic-user-1'])]
    public function getTopicUser1(): TopicUser
    {
        return $this->topicUser[0];
    }

    public function getSluggableFields(): array
    {
        return ['libTopic'];
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
