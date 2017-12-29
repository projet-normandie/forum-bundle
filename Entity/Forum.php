<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Forum
 *
 * @ORM\Table(name="forum_forum", indexes={@ORM\Index(name="idxPosition", columns={"position"})})
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\ForumRepository")
 */
class Forum
{
    use Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="idForum", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idForum;

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
     *   @ORM\JoinColumn(name="idCategory", referencedColumnName="idCategory")
     * })
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\Topic", mappedBy="forum")
     */
    private $topics;

    /**
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%s [%s]', $this->getLibForum(), $this->getIdForum());
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->topics = new ArrayCollection();
    }

    /**
     * Set idForum
     *
     * @param integer $idForum
     * @return Forum
     */
    public function setIdForum($idForum)
    {
        $this->idForum = $idForum;
        return $this;
    }

    /**
     * Get idForum
     *
     * @return integer
     */
    public function getIdForum()
    {
        return $this->idForum;
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
     * Set nbTopic
     *
     * @param integer $nbTopic
     * @return $this
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
}
