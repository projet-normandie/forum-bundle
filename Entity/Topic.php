<?php

namespace ProjetNormandie\ForumBundle\Entity;

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
use ApiPlatform\Core\Serializer\Filter\GroupFilter;

/**
 * Topic
 *
 * @ORM\Table(name="forum_topic")
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\TopicRepository")
 * @ORM\EntityListeners({"ProjetNormandie\ForumBundle\EventListener\Entity\TopicListener"})
 * @ApiResource(attributes={"order"={"type.position": "ASC","lastMessage.id": "DESC"}})
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
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(min="5")
     * @Assert\Length(max="255")
     * @ORM\Column(name="libTopic", type="string", length=255, nullable=false)
     */
    private $libTopic;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbMessage", type="integer", nullable=false, options={"default":0})
     */
    private $nbMessage = 0;


    /**
     * @var Forum
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Forum", inversedBy="topics")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idForum", referencedColumnName="id")
     * })
     */
    private $forum;

    /**
     * @var UserInterface
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\UserInterface")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var TopicType
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\TopicType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idType", referencedColumnName="id")
     * })
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\Message", mappedBy="topic", cascade={"persist"})
     */
    private $messages;

    /**
     * @var Message
     *
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Message")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idMessageMax", referencedColumnName="id")
     * })
     */
    private $lastMessage;

    /**
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\TopicUser", mappedBy="topic")
     */
    private $topicUser;

    /**
     * Shortcut to topicUser.boolRead
     * @var boolean
     */
    private $boolRead;

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
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libTopic
     *
     * @param string $libTopic
     * @return Topic
     */
    public function setLibTopic(string $libTopic)
    {
        $this->libTopic = $libTopic;

        return $this;
    }

    /**
     * Get libTopic
     *
     * @return string
     */
    public function getLibTopic()
    {
        return $this->libTopic;
    }

    /**
     * Set nbMessage
     *
     * @param integer $nbMessage
     * @return $this
     */
    public function setNbMessage(int $nbMessage)
    {
        $this->nbMessage = $nbMessage;

        return $this;
    }

    /**
     * Get nbMessage
     *
     * @return integer
     */
    public function getNbMessage()
    {
        return $this->nbMessage;
    }

    /**
     * Set forum
     * @param Forum|null $forum
     * @return $this
     */
    public function setForum(Forum $forum = null)
    {
        $this->forum = $forum;
        return $this;
    }

    /**
     * Get forum
     * @return Forum
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * Set user
     * @param null $user
     * @return $this
     */
    public function setUser($user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set type
     * @param TopicType|null $type
     * @return $this
     */
    public function setType(TopicType $type = null)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     * @return TopicType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set messages
     * @param array|null $messages
     * @return $this
     */
    public function setMessages(array $messages = null)
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
     * @return mixed
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param Message|null $message
     * @return $this
     */
    public function setLastMessage(Message $message = null)
    {
        $this->lastMessage = $message;
        return $this;
    }

    /**
     * Get lastMessage
     * @return Message
     */
    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    /**
     * @return mixed
     */
    public function getTopicUser()
    {
        return $this->topicUser;
    }

    /**
     * @param $boolRead
     * @return $this
     */
    public function setBoolRead($boolRead)
    {
        $this->boolRead = $boolRead;
        return $this;
    }

    /**
     * @return bool
     */
    public function getBoolRead()
    {
        return $this->boolRead;
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
    public function getUrl()
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
