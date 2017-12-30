<?php

namespace ProjetNormandie\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Language
 *
 * @ORM\Table(name="forum_language")
 * @ORM\Entity(repositoryClass="ProjetNormandie\ForumBundle\Repository\LanguageRepository")
 */
class Language
{

    /**
     * @var integer
     *
     * @ORM\Column(name="idLanguage", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idLanguage;

    /**
     * @var string
     *
     * @Assert\Length(max="30")
     * @ORM\Column(name="libLanguage", type="string", length=30, nullable=false)
     */
    private $libLanguage;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=30, nullable=false)
     */
    private $code;

    /**
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%s [%s]', $this->getLibLanguage(), $this->getIdLanguage());
    }

    /**
     * Set idLanguage
     *
     * @param integer $idLanguage
     * @return Language
     */
    public function setIdLanguage($idLanguage)
    {
        $this->idLanguage = $idLanguage;
        return $this;
    }

    /**
     * Get idLanguage
     *
     * @return integer
     */
    public function getIdLanguage()
    {
        return $this->idLanguage;
    }

    /**
     * Set libLanguage
     *
     * @param string $libLanguage
     * @return Language
     */
    public function setLibLanguage($libLanguage)
    {
        $this->libLanguage = $libLanguage;

        return $this;
    }

    /**
     * Get libLanguage
     *
     * @return string
     */
    public function getLibLanguage()
    {
        return $this->libLanguage;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
