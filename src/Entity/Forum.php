<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;

/**
 * Forum
 *
 * @ORM\Table(
 *     name="forum_forum",
 *     indexes={
 *         @ORM\Index(name="idx_position", columns={"position"}),
 *         @ORM\Index(name="idx_libForum", columns={"libForum"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\ForumRepository")
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "parent": "exact",
 *     }
 * )
 * @ApiFilter(DateFilter::class, properties={"lastMessage.createdAt": DateFilter::EXCLUDE_NULL})
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "lastMessage.id":"DESC",
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *          "parameterName": "groups",
 *          "overrideDefaultGroups": true,
 *          "whitelist": {
 *              "forum.forum.read",
 *              "forum.lastMessage",
 *              "forum.message.last",
 *              "forum.forum.forumUser1",
 *              "forum.forumUser.read"
 *          }
 *     }
 * )
 */
class Forum implements TimestampableInterface, SluggableInterface
{
    use TimestampableTrait;
    use SluggableTrait;

    const STATUS_PRIVATE = 'private';
    const STATUS_PUBLIC = 'public';

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @Assert\Length(max="255")
     * @ORM\Column(name="libForum", type="string", length=255, nullable=false)
     */
    private string $libForum;

    /**
     * @Assert\Length(max="255")
     * @ORM\Column(name="libForumFr", type="string", length=255, nullable=true)
     */
    private string $libForumFr;

    /**
     * @ORM\Column(name="position", type="integer", nullable=true, options={"default":0})
     */
    private int $position = 0;

    /**
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private string $status = self::STATUS_PUBLIC;

    /**
     * @ORM\Column(name="role", type="string", nullable=true)
     */
    private ?string $role = null;

    /**
     * @ORM\Column(name="nbMessage", type="integer", nullable=false, options={"default":0})
     */
    private int $nbMessage = 0;

    /**
     * @ORM\Column(name="nbTopic", type="integer", nullable=false, options={"default":0})
     */
    private int $nbTopic = 0;

    /**
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Category", inversedBy="forums")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCategory", referencedColumnName="id")
     * })
     */
    private ?Category $category;

    /**
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\Topic", mappedBy="forum")
     */
    private Collection $topics;

    /**
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\Forum", mappedBy="parent")
     */
    private Collection $children;

    /**
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Forum", inversedBy="children")
     * @ORM\JoinColumn(name="idParent", referencedColumnName="id")
     */
    private ?Forum $parent;

    /**
     * @ORM\Column(name="isParent", type="boolean", nullable=false, options={"default":false})
     */
    private bool $isParent = false;

    /**
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Message", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idMessageMax", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private Message $lastMessage;

    /**
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\ForumUser", mappedBy="forum")
     */
    private Collection $forumUser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->forumUser = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getLibForum(), $this->getId());
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Forum
     */
    public function setId(int $id): Forum
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set libForum
     *
     * @param string $libForum
     * @return Forum
     */
    public function setLibForum(string $libForum): Forum
    {
        $this->libForum = $libForum;

        return $this;
    }

    /**
     * Get libForum
     *
     * @return string
     */
    public function getLibForum(): string
    {
        return $this->libForum;
    }

     /**
     * Set libForumFr
     *
     * @param string $libForumFr
     * @return Forum
     */
    public function setLibForumFr(string $libForumFr): Forum
    {
        $this->libForumFr = $libForumFr;

        return $this;
    }

    /**
     * Get libForumFr
     *
     * @return string
     */
    public function getLibForumFr(): string
    {
        return $this->libForumFr;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Forum
     */
    public function setPosition(int $position): Forum
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Forum
     */
    public function setStatus(string $status): Forum
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }


    /**
     * Set role
     *
     * @param string $role
     * @return Forum
     */
    public function setRole(string $role): Forum
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * Set nbMessage
     *
     * @param integer $nbMessage
     * @return Forum
     */
    public function setNbMessage(int $nbMessage): Forum
    {
        $this->nbMessage = $nbMessage;

        return $this;
    }

    /**
     * Get nbMessage
     *
     * @return integer
     */
    public function getNbMessage(): int
    {
        return $this->nbMessage;
    }

    /**
     * Set nbTopic
     *
     * @param integer $nbTopic
     * @return Forum
     */
    public function setNbTopic(int $nbTopic): Forum
    {
        $this->nbTopic = $nbTopic;

        return $this;
    }

    /**
     * Get nbTopic
     *
     * @return integer
     */
    public function getNbTopic()
    {
        return $this->nbTopic;
    }

    /**
     * Set category
     * @param Category|null $category
     * @return $this
     */
    public function setCategory(Category $category = null): Forum
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }


    /**
     * Set parent
     * @param Forum|null $forum
     * @return Forum|null
     */
    public function setParent(Forum $forum = null): ?Forum
    {
        $this->parent = $forum;
        return $this;
    }

    /**
     * Get parent
     * @return ?Forum
     */
    public function getParent(): ?Forum
    {
        return $this->parent;
    }

    /**
     * @return Collection
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

     /**
     * @return Collection
     */
    public function getChildrens(): Collection
    {
        return $this->children;
    }

    /**
     * @param Message|null $message
     * @return $this
     */
    public function setLastMessage(Message $message = null): Forum
    {
        $this->lastMessage = $message;
        return $this;
    }

    /**
     * Get lastMessage
     * @return Message
     */
    public function getLastMessage(): Message
    {
        return $this->lastMessage;
    }

    /**
     * @return Collection
     */
    public function getForumUser(): Collection
    {
        return $this->forumUser;
    }

     /**
     * Set isParent
     *
     * @param boolean $isParent
     * @return $this
     */
    public function setIsParent(bool $isParent): Forum
    {
        $this->isParent= $isParent;

        return $this;
    }

    /**
     * Get isParent
     *
     * @return boolean
     */
    public function getIsParent(): bool
    {
        return $this->isParent;
    }

     /**
     * @return mixed
     */
    public function getForumUser1()
    {
        return $this->forumUser[0];
    }


    /**
     * @return array
     */
    public static function getStatusChoices(): array
    {
        return [
            self::STATUS_PRIVATE => self::STATUS_PRIVATE,
            self::STATUS_PUBLIC => self::STATUS_PUBLIC,
        ];
    }

    /**
     * Returns an array of the fields used to generate the slug.
     *
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['libForum'];
    }
}
