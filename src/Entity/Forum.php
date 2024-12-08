<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Entity;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\Serializer\Filter\GroupFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use ProjetNormandie\ForumBundle\Controller\ReadForum;
use ProjetNormandie\ForumBundle\Repository\ForumRepository;
use ProjetNormandie\ForumBundle\ValueObject\ForumStatus;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name:'pnf_forum')]
#[ORM\Entity(repositoryClass: ForumRepository::class)]
#[ORM\EntityListeners(["ProjetNormandie\ForumBundle\EventListener\Entity\ForumListener"])]
#[ORM\Index(name: "idx_position", columns: ["position"])]
#[ORM\Index(name: "idx_lib_forum", columns: ["lib_forum"])]
#[ApiResource(
    shortName: 'ForumForum',
    operations: [
        new GetCollection(
            uriTemplate: '/forum_forums',
        ),
        new Get(
            uriTemplate: '/forum_forums/{id}',
            security: 'object.getStatus() == "public" or is_granted(object.getRole())',
        ),
        new Get(
            uriTemplate: '/forum_forums/{id}/read',
            controller: ReadForum::class,
            security: 'is_granted("ROLE_USER")',
            openapi: new Model\Operation(
                summary: 'Mark forum as read',
                description: 'Mark forum as read'
            ),
        ),
    ],
    normalizationContext: ['groups' => ['forum:read']
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'parent' => 'exact',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'lastMessage.id' => 'DESC'
    ]
)]
#[ApiFilter(
    GroupFilter::class,
    arguments: [
        'parameterName' => 'groups',
        'overrideDefaultGroups' => true,
        'whitelist' => [
            'forum:read',
            'forum:user',
            'forum:last-message',
            'message:user',
            'message:read',
            'forum:forum-user-1',
            'forum-user:read'
        ]
    ]
)]
#[ApiFilter(DateFilter::class, properties: ['lastMessage.createdAt' => DateFilterInterface::EXCLUDE_NULL])]
class Forum implements TimestampableInterface, SluggableInterface
{
    use TimestampableTrait;
    use SluggableTrait;

    #[Groups(['forum:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private int $id;

    #[Groups(['forum:read'])]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $libForum;

    #[Groups(['forum:read'])]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private string $libForumFr;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private int $position = 0;

    #[Groups(['forum:read'])]
    #[ORM\Column(length: 20, nullable: false)]
    private string $status = ForumStatus::PUBLIC;

    #[Groups(['forum:read'])]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $role = null;

    #[Groups(['forum:read'])]
    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private int $nbMessage = 0;

    #[Groups(['forum:read'])]
    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private int $nbTopic = 0;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'forums')]
    #[ORM\JoinColumn(name:'category_id', referencedColumnName:'id', nullable:true)]
    private ?Category $category;

    #[ORM\OneToMany(targetEntity: Topic::class, mappedBy: 'forum')]
    private Collection $topics;

    #[ORM\OneToMany(targetEntity: Forum::class, mappedBy: 'parent')]
    private Collection $childrens;

    #[Groups(['forum:read'])]
    #[ORM\ManyToOne(targetEntity: Forum::class, inversedBy: 'childrens')]
    #[ORM\JoinColumn(name:'parent_id', referencedColumnName:'id', nullable:true)]
    private ?Forum $parent;

    #[Groups(['forum:read'])]
    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $isParent = false;

    #[Groups(['forum:last-message'])]
    #[ORM\ManyToOne(targetEntity: Message::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name:'max_message_id', referencedColumnName:'id', nullable:true, onDelete: 'SET NULL')]
    private ?Message $lastMessage;

    #[Groups(['forum:forum-user'])]
    #[ORM\OneToMany(targetEntity: ForumUser::class, mappedBy: 'forum')]
    private Collection $forumUser;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->forumUser = new ArrayCollection();
        $this->childrens = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('%s [%s]', $this->getLibForum(), $this->getId());
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLibForum(string $libForum): void
    {
        $this->libForum = $libForum;
    }

    public function getLibForum(): string
    {
        return $this->libForum;
    }

    public function setLibForumFr(string $libForumFr): void
    {
        $this->libForumFr = $libForumFr;
    }

    public function getLibForumFr(): string
    {
        return $this->libForumFr;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setNbMessage(int $nbMessage): void
    {
        $this->nbMessage = $nbMessage;
    }

    public function getNbMessage(): int
    {
        return $this->nbMessage;
    }

    public function setNbTopic(int $nbTopic): void
    {
        $this->nbTopic = $nbTopic;
    }


    public function getNbTopic(): int
    {
        return $this->nbTopic;
    }

    public function setCategory(Category $category = null): void
    {
        $this->category = $category;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setParent(Forum $forum = null): void
    {
        $this->parent = $forum;
    }

    public function getParent(): ?Forum
    {
        return $this->parent;
    }

    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function getChildrens(): Collection
    {
        return $this->childrens;
    }

    public function setLastMessage(Message $message = null): void
    {
        $this->lastMessage = $message;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function getForumUser(): Collection
    {
        return $this->forumUser;
    }

    public function setIsParent(bool $isParent): void
    {
        $this->isParent = $isParent;
    }

    public function getIsParent(): bool
    {
        return $this->isParent;
    }

    #[Groups(['forum:forum-user-1'])]
    public function getForumUser1()
    {
        return $this->forumUser[0];
    }

    public function getSluggableFields(): array
    {
        return ['libForum'];
    }
}
