<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Topic
 *
 * @ORM\Table(name="forum_message")
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\MessageRepository")
 */
class Message
{
    use Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="idMessage", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idMessage;

    /**
     * @var string
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var Topic
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Topic", inversedBy="messages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTopic", referencedColumnName="idTopic")
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
        return \sprintf('[%s]', $this->getIdMessage());
    }

    /**
     * Set idMessage
     *
     * @param integer $idMessage
     * @return $this
     */
    public function setIdMessage($idMessage)
    {
        $this->idMessage = $idMessage;
        return $this;
    }

    /**
     * Get idMessage
     *
     * @return integer
     */
    public function getIdMessage()
    {
        return $this->idMessage;
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
     * Get user
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
