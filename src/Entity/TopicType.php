<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Type
 *
 * @ORM\Table(name="forum_topic_type")
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\TopicTypeRepository")
 */
class TopicType
{
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
     *
     * @Assert\Length(max="30")
     * @ORM\Column(name="libType", type="string", length=30, nullable=false)
     */
    private $libType;

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
        return sprintf('%s [%s]', $this->getLibType(), $this->getId());
    }


    /**
     * Set id
     *
     * @param integer $id
     * @return TopicType
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get idType
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libType
     *
     * @param string $libType
     * @return TopicType
     */
    public function setLibType(string $libType)
    {
        $this->libType = $libType;

        return $this;
    }

    /**
     * Get libType
     *
     * @return string
     */
    public function getLibType()
    {
        return $this->libType;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return $this
     */
    public function setPosition(int $position)
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
}
