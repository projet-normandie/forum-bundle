<?php

namespace ProjetNormandie\ForumBundle\Entity;

/**
 * Interface that defines the rules that must respect the User objects instances.
 */
interface UserInterface
{
    /** @return int  */
    public function getId();
    /** @return string */
    public function getLocale();
    /**
     * @return string
     */
    public function getEmail();
    /**
     * @return string
     */
    public function getStatus();
    /**
     * @return string
     */
    public function getPseudo();
    /**
     * @return integer
     */
    public function getNbForumMessage();
    /**
     * @return string
     */
    public function __toString();
}
