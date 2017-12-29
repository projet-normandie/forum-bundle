<?php

namespace ProjetNormandie\ForumBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Topic
 *
 * @ORM\Table(name="forum_topic")
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\TopicRepository")
 */
class Topic
{
    use Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="idTopic", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTopic;

    /**
     * @var string
     *
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
     *   @ORM\JoinColumn(name="idForum", referencedColumnName="idForum")
     * })
     */
    private $forum;

    /**
     * @var User
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
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
     *   @ORM\JoinColumn(name="idType", referencedColumnName="idType")
     * })
     */
    private $type;

    /**
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%s [%s]', $this->getLibTopic(), $this->getIdTopic());
    }

    /**
     * Set idTopic
     *
     * @param integer $idTopic
     * @return Topic
     */
    public function setIdTopic($idTopic)
    {
        $this->idTopic = $idTopic;
        return $this;
    }

    /**
     * Get idTopic
     *
     * @return integer
     */
    public function getIdTopic()
    {
        return $this->idTopic;
    }

    /**
     * Set libTopic
     *
     * @param string $libTopic
     * @return Topic
     */
    public function setLibTopic($libTopic)
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
     * Set forum
     * @param Forum $forum
     * @return Topic
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
     * Get user
     * @return User
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


}
