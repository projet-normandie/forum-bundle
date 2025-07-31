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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use ProjetNormandie\ForumBundle\Controller\Forum\MarkAsRead;
use ProjetNormandie\ForumBundle\Repository\ForumRepository;
use ProjetNormandie\ForumBundle\ValueObject\ForumStatus;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name:'pnf_forum')]
#[ORM\Entity(repositoryClass: ForumRepository::class)]
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
            uriTemplate: '/forum_forums/{id}/mark-as-read',
            controller: MarkAsRead::class,
            security: 'is_granted("ROLE_USER")',
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
#[ApiFilter(DateFilter::class, properties: ['lastMessage.createdAt' => DateFilterInterface::EXCLUDE_NULL])]
class Forum
{
    use TimestampableEntity;

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

    #[Groups(['forum:read'])]
    #[ORM\Column(length: 128)]
    #[Gedmo\Slug(fields: ['libForum'])]
    protected string $slug;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'forums')]
    #[ORM\JoinColumn(name:'category_id', referencedColumnName:'id', nullable:true)]
    private ?Category $category;

    #[ORM\OneToMany(targetEntity: Topic::class, mappedBy: 'forum')]
    private Collection $topics;

    #[Groups(['forum:last-message'])]
    #[ORM\ManyToOne(targetEntity: Message::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name:'max_message_id', referencedColumnName:'id', nullable:true, onDelete: 'SET NULL')]
    private ?Message $lastMessage;

    #[Groups(['forum:forum-user'])]
    #[ORM\OneToMany(targetEntity: ForumUserLastVisit::class, mappedBy: 'forum')]
    private Collection $userLastVisits;

    #[Groups(['forum:read-status'])]
    public ?int $unreadTopicsCount = null;

    #[Groups(['forum:read-status'])]
    public ?bool $isUnread = null;

    #[Groups(['forum:read-status'])]
    public ?bool $hasNewContent = null;

    #[Groups(['forum:read-status'])]
    public ?bool $hasBeenVisited = null;


    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->userLastVisits = new ArrayCollection();
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setCategory(?Category $category = null): void
    {
        $this->category = $category;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }


    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function setLastMessage(?Message $message = null): void
    {
        $this->lastMessage = $message;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function getLastVisitData(): ?ForumUserLastVisit
    {
        if ($this->userLastVisits->first()) {
            return $this->userLastVisits->first();
        }
        return null;
    }

    #[Groups(['forum:read-status'])]
    public function getHasNewContent(): ?bool
    {
        $forumVisit = $this->getLastVisitData();
        if ($forumVisit && $this->getLastMessage()) {
            return $this->getLastMessage()->getCreatedAt() > $forumVisit->getLastVisitedAt();
        } else {
            return $this->getLastMessage() !== null;
        }
    }

    #[Groups(['forum:read-status'])]
    public function getHasBeenVisited(): ?bool
    {
        $forumVisit = $this->getLastVisitData();
        return $forumVisit !== null;
    }

    #[Groups(['forum:read-status'])]
    public function getLastVisitedAt(): ?\DateTime
    {
        $forumVisit = $this->getLastVisitData();
        return $forumVisit?->getLastVisitedAt();
    }

}
