<?php

namespace ProjetNormandie\ForumBundle\Entity;

/**
 * Interface that defines the rules that must respect the User objects instances.
 */
interface UserInterface
{
    /** @return int  */
    public function getId(): int;
    /** @return string */
    public function getLocale(): string;
    /**
     * @return string
     */
    public function getEmail(): string;
    /**
     * @return string
     */
    public function getStatus(): string;
    /**
     * @return string
     */
    public function getPseudo(): string;
    /**
     * @return integer
     */
    public function getNbForumMessage(): int;
    /**
     * @return string
     */
    public function __toString();
}
