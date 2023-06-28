<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;

/**
 * Topic
 *
 * @ORM\Table(
 *     name="forum_topic",
 *     indexes={
 *         @ORM\Index(name="idx_libTopic", columns={"libTopic"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\TopicRepository")
 * @ORM\EntityListeners({"ProjetNormandie\ForumBundle\EventListener\Entity\TopicListener"})
 * @ApiResource(
 * *     attributes={"order"={"type.position": "ASC","lastMessage.id": "DESC"}}
 * )
 * @ApiFilter(BooleanFilter::class, properties={"boolArchive"})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "libTopic": "partial",
 *          "forum": "exact",
 *      }
 * )
 */
class Topic implements TimestampableInterface, SluggableInterface
{
    use TimestampableTrait;
    use SluggableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min="5")
     * @Assert\Length(max="255")
     * @ORM\Column(name="libTopic", type="string", length=255, nullable=false)
     */
    private string $libTopic;

    /**
     * @ORM\Column(name="nbMessage", type="integer", nullable=false, options={"default":0})
     */
    private int $nbMessage = 0;


    /**
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Forum", inversedBy="topics")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idForum", referencedColumnName="id")
     * })
     */
    private Forum $forum;

    /**
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\UserInterface")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\TopicType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idType", referencedColumnName="id")
     * })
     */
    private TopicType $type;

    /**
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\Message", mappedBy="topic", cascade={"persist"})
     */
    private Collection $messages;

    /**
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Message", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idMessageMax", referencedColumnName="id")
     * })
     */
    private Message $lastMessage;

    /**
     * @ORM\Column(name="boolArchive", type="boolean", nullable=false, options={"default":0})
     */
    private bool $boolArchive = false;

    /**
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\TopicUser", mappedBy="topic")
     */
    private Collection $topicUser;


    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getLibTopic(), $this->getId());
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->topicUser = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Topic
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set libTopic
     *
     * @param string $libTopic
     * @return Topic
     */
    public function setLibTopic(string $libTopic): self
    {
        $this->libTopic = $libTopic;

        return $this;
    }

    /**
     * Get libTopic
     *
     * @return string
     */
    public function getLibTopic(): string
    {
        return $this->libTopic;
    }

    /**
     * Set nbMessage
     *
     * @param integer $nbMessage
     * @return $this
     */
    public function setNbMessage(int $nbMessage): self
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
     * Set forum
     * @param Forum $forum
     * @return $this
     */
    public function setForum(Forum $forum): self
    {
        $this->forum = $forum;
        return $this;
    }

    /**
     * Get forum
     * @return Forum
     */
    public function getForum(): Forum
    {
        return $this->forum;
    }

    /**
     * Set user
     * @param $user
     * @return $this
     */
    public function setUser($user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set type
     * @param TopicType $type
     * @return $this
     */
    public function setType(TopicType $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     * @return TopicType
     */
    public function getType(): TopicType
    {
        return $this->type;
    }

    /**
     * Set messages
     * @param array $messages
     * @return $this
     */
    public function setMessages(array $messages): self
    {
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
        return $this;
    }

    /**
     * @param Message $message
     */
    public function addMessage(Message $message)
    {
        $message->setTopic($this);
        $this->messages[] = $message;
    }

    /**
     * @return Collection
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * @param Message|null $message
     * @return $this
     */
    public function setLastMessage(Message $message = null): self
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
     * Set boolArchive
     *
     * @param boolean $boolArchive
     * @return $this
     */
    public function setBoolArchive(bool $boolArchive): self
    {
        $this->boolArchive = $boolArchive;

        return $this;
    }

    /**
     * Get boolArchive
     *
     * @return boolean
     */
    public function getBoolArchive(): bool
    {
        return $this->boolArchive;
    }

    /**
     * @return Collection
     */
    public function getTopicUser()
    {
        return $this->topicUser;
    }

    /**
     * @return TopicUser
     */
    public function getTopicUser1(): TopicUser
    {
        return $this->topicUser[0];
    }

    /**
     * Returns an array of the fields used to generate the slug.
     *
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['libTopic'];
    }

    /**
     * @return string
     */
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
