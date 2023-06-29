<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Topic
 *
 * @ORM\Table(name="forum_topic_user")
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\TopicUserRepository")
 */
class TopicUser
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

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
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Topic", inversedBy="topicUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTopic", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private Topic $topic;

    /**
     * @ORM\Column(name="boolRead", type="boolean", nullable=false, options={"default":0})
     */
    private bool $boolRead = false;

    /**
     * @ORM\Column(name="boolNotif", type="boolean", nullable=false, options={"default":0})
     */
    private bool $boolNotif = false;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s - %s', $this->getUser(), $this->getTopic());
    }


    /**
     * Set id
     *
     * @param integer $id
     * @return TopicUser
     */
    public function setId(int $id): TopicUser
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
     * Set user
     *
     * @param $user
     * @return $this
     */
    public function setUser($user = null): TopicUser
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set topic
     * @param Topic|null $topic
     * @return $this
     */
    public function setTopic(Topic $topic = null): TopicUser
    {
        $this->topic = $topic;
        return $this;
    }

    /**
     * Get topic
     *
     * @return Topic
     */
    public function getTopic(): Topic
    {
        return $this->topic;
    }

    /**
     * Set boolRead
     *
     * @param boolean $boolRead
     * @return $this
     */
    public function setBoolRead(bool $boolRead): TopicUser
    {
        $this->boolRead = $boolRead;

        return $this;
    }

    /**
     * Get boolRead
     *
     * @return boolean
     */
    public function getBoolRead(): bool
    {
        return $this->boolRead;
    }

    /**
     * Set boolNotif
     *
     * @param boolean $boolNotif
     * @return $this
     */
    public function setBoolNotif(bool $boolNotif): TopicUser
    {
        $this->boolNotif = $boolNotif;

        return $this;
    }

    /**
     * Get boolNotif
     *
     * @return boolean
     */
    public function getBoolNotif(): bool
    {
        return $this->boolNotif;
    }
}
