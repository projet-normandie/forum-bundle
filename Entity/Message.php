<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;;

/**
 * Message
 *
 * @ORM\Table(name="forum_message")
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\MessageRepository")
 * @ApiResource(attributes={"order"={"id": "ASC"}})
 * @ApiFilter(OrderFilter::class, properties={"id": "ASC"}, arguments={"orderParameterName"="order"})
 *
 */
class Message implements TimestampableInterface
{
    use TimestampableTrait;

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
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var Topic
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Topic", inversedBy="messages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTopic", referencedColumnName="id")
     * })
     */
    private $topic;

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
     * @return string
     */
    public function __toString()
    {
        return \sprintf('[%s]', $this->getId());
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return $this
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
     * Set message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set topic
     * @param Topic $topic
     * @return $this
     */
    public function setTopic(Topic $topic = null)
    {
        $this->topic = $topic;
        return $this;
    }

    /**
     * Get topic
     * @return Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set user
     * @param UserInterface $user
     * @return Message
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
}
