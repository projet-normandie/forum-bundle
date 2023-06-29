<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * Category
 *
 * @ORM\Table(name="forum_category")
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\CategoryRepository")
 */
class Category implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @Assert\Length(max="50")
     * @ORM\Column(name="libCategory", type="string", length=50, nullable=false)
     */
    private string $libCategory;

    /**
     * @ORM\Column(name="position", type="integer", nullable=true, options={"default":0})
     */
    private int $position = 0;

    /**
     * @ORM\OneToMany(targetEntity="ProjetNormandie\ForumBundle\Entity\Forum", mappedBy="category")
     */
    private Collection $forums;


    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getLibCategory(), $this->getId());
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->forums = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Category
     */
    public function setId(int $id): Category
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
     * Set libCategory
     *
     * @param string $libCategory
     * @return Category
     */
    public function setLibCategory(string $libCategory): Category
    {
        $this->libCategory = $libCategory;

        return $this;
    }

    /**
     * Get libCategory
     *
     * @return string
     */
    public function getLibCategory(): string
    {
        return $this->libCategory;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Category
     */
    public function setPosition(int $position): Category
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
     * @return Collection
     */
    public function getForums(): Collection
    {
        return $this->forums;
    }
}
