<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Category
 *
 * @ORM\Table(name="forum_category", indexes={@ORM\Index(name="idxPosition", columns={"position"})})
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\CategoryRepository")
 */
class Category
{
    use Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="idCategory", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCategory;

    /**
     * @var string
     *
     * @Assert\Length(max="50")
     * @ORM\Column(name="libCategory", type="string", length=50, nullable=false)
     */
    private $libCategory;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=true, options={"default":0})
     */
    private $position = 0;

    /**
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\Forum", mappedBy="category")
     */
    private $forums;


    /**
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%s [%s]', $this->getLibCategory(), $this->getIdCategory());
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->forums = new ArrayCollection();
    }

    /**
     * Set idCategory
     *
     * @param integer $idCategory
     * @return Category
     */
    public function setIdCategory($idCategory)
    {
        $this->idCategory = $idCategory;
        return $this;
    }

    /**
     * Get idCategory
     *
     * @return integer
     */
    public function getIdCategory()
    {
        return $this->idCategory;
    }

    /**
     * Set libCategory
     *
     * @param string $libCategory
     * @return Category
     */
    public function setLibCategory($libCategory)
    {
        $this->libCategory = $libCategory;

        return $this;
    }

    /**
     * Get libCategory
     *
     * @return string
     */
    public function getLibCategory()
    {
        return $this->libCategory;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Category
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
     * @return mixed
     */
    public function getForums()
    {
        return $this->forums;
    }
}
