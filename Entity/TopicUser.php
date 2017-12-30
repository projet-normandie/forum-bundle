<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Topic
 *
 * @ORM\Table(name="forum_topic_user")
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\TopicUserRepository")
 */
class TopicUser
{

    /**
     * @var UserInterface
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\UserInterface")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var Topic
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Topic")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTopic", referencedColumnName="idTopic")
     * })
     */
    private $topic;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolRead", type="boolean", nullable=false, options={"default":0})
     */
    private $boolRead = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolNotif", type="boolean", nullable=false, options={"default":0})
     */
    private $boolNotif = false;

    /**
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%s - %s', $this->getUser(), $this->getTopic());
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user = null)
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
     *
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
     *
     * @return Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set boolRead
     *
     * @param boolean $boolRead
     * @return $this
     */
    public function setBoolRead($boolRead)
    {
        $this->boolRead = $boolRead;

        return $this;
    }

    /**
     * Get boolRead
     *
     * @return boolean
     */
    public function getBoolRead()
    {
        return $this->boolRead;
    }

    /**
     * Set boolNotif
     *
     * @param boolean $boolNotif
     * @return $this
     */
    public function setBoolNotif($boolNotif)
    {
        $this->boolNotif = $boolNotif;

        return $this;
    }

    /**
     * Get boolNotif
     *
     * @return boolean
     */
    public function getBoolNotif()
    {
        return $this->boolNotif;
    }
}
