<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;

/**
 * Message
 *
 * @ORM\Table(name="forum_message")
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\MessageRepository")
 * @ORM\EntityListeners({"ProjetNormandie\ForumBundle\EventListener\Entity\MessageListener"})
 * @ApiResource(attributes={"order"={"id": "ASC"}})
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id": "ASC",
 *          "createdAt": "DESC"
 *      },
 *     arguments={"orderParameterName"="order"}
 * )
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "topic": "exact",
 *          "user": "exact",
 *          "topic.forum.status": "exact",
 *      }
 * )
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *          "parameterName": "groups",
 *          "overrideDefaultGroups": true,
 *          "whitelist": {
 *              "forum.message.read",
 *              "forum.message.topic",
 *              "forum.topic.read",
 *              "forum.topic.forum",
 *              "forum.forum.read",
 *              "forum.user.read",
 *              "forum.user.status",
 *              "user.status.read"
 *          }
 *     }
 * )
 */
class Message implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private string $message;


    /**
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private int $position = 1;

    /**
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Topic", inversedBy="messages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTopic", referencedColumnName="id")
     * })
     */
    private Topic $topic;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\UserInterface")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('[%s]', $this->getId());
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): Message
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
     * Set message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): Message
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Set topic
     * @param Topic|null $topic
     * @return $this
     */
    public function setTopic(Topic $topic = null): Message
    {
        $this->topic = $topic;
        return $this;
    }

    /**
     * Get topic
     * @return Topic
     */
    public function getTopic(): Topic
    {
        return $this->topic;
    }

    /**
     * Set user
     * @param $user
     * @return $this
     */
    public function setUser($user): Message
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
     * Set position
     * @param integer $position
     * @return $this
     */
    public function setPosition(int $position): Message
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
     * @return int
     */
    public function getPage(): int
    {
        return (int) floor(($this->getPosition() -1) / 20) + 1;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->getTopic()->getUrl() . '?page=' . $this->getPage() . '#' . $this->getId();
    }
}
