<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Forum
 *
 * @ORM\Table(name="forum_forum", indexes={@ORM\Index(name="idxPosition", columns={"position"})})
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\ForumRepository")
 */
class Forum
{
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
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%s [%s]', $this->getLibForum(), $this->getIdForum());
    }

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
}
