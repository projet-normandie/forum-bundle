<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;

/**
 * Forum
 *
 * @ORM\Table(name="forum_forum", indexes={@ORM\Index(name="idxPosition", columns={"position"})})
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\ForumRepository")
 */
class Forum implements TimestampableInterface, SluggableInterface
{
    use TimestampableTrait;
    use SluggableTrait;

    const STATUS_PRIVATE = 'private';
    const STATUS_PUBLIC = 'public';

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
     * @Assert\Length(max="50")
     * @ORM\Column(name="libForum", type="string", length=50, nullable=false)
     */
    private $libForum;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=true, options={"default":0})
     */
    private $position = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_PUBLIC;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", nullable=true)
     */
    private $role = null;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbMessage", type="integer", nullable=false, options={"default":0})
     */
    private $nbMessage = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbTopic", type="integer", nullable=false, options={"default":0})
     */
    private $nbTopic = 0;

    /**
     * @var Category
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Category", inversedBy="forums")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCategory", referencedColumnName="id")
     * })
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\Topic", mappedBy="forum")
     */
    private $topics;

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
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\ForumUser", mappedBy="forum")
     */
    private $forumUser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->forumUser = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%s [%s]', $this->getLibForum(), $this->getId());
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Forum
     */
    public function setId($id)
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
     * Set libForum
     *
     * @param string $libForum
     * @return Forum
     */
    public function setLibForum($libForum)
    {
        $this->libForum = $libForum;

        return $this;
    }

    /**
     * Get libForum
     *
     * @return string
     */
    public function getLibForum()
    {
        return $this->libForum;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Forum
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Forum
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * Set role
     *
     * @param string $role
     * @return Forum
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set nbMessage
     *
     * @param integer $nbMessage
     * @return Forum
     */
    public function setNbMessage($nbMessage)
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
     * Set nbTopic
     *
     * @param integer $nbTopic
     * @return Forum
     */
    public function setNbTopic($nbTopic)
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
     * @param Category $category
     * @return Forum
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getTopics()
    {
        return $this->topics;
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
    public function getForumUser()
    {
        return $this->forumUser;
    }


    /**
     * @return array
     */
    public static function getStatusChoices()
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
