<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Topic
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="forum_forum_user")
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\ForumUserRepository")
 */
class ForumUser
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
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\ForumBundle\Entity\Forum", inversedBy="forumUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idForum", referencedColumnName="id")
     * })
     */
    private Forum $forum;

    /**
     * @ORM\Column(name="boolRead", type="boolean", nullable=false, options={"default":0})
     */
    private bool $boolRead = false;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s - %s', $this->getUser(), $this->getForum());
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return ForumUser
     */
    public function setId(int $id): ForumUser
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
    public function setUser($user = null): ForumUser
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
     * Set forum
     * @param Forum $forum
     * @return $this
     */
    public function setForum(Forum $forum): ForumUser
    {
        $this->forum = $forum;
        return $this;
    }

    /**
     * Get forum
     *
     * @return Forum
     */
    public function getForum(): Forum
    {
        return $this->forum;
    }

    /**
     * Set boolRead
     *
     * @param boolean $boolRead
     * @return $this
     */
    public function setBoolRead(bool $boolRead): ForumUser
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
}
