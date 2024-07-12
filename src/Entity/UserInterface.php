<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Entity;

interface UserInterface
{
    public function getId(): int;
    public function getLocale(): string;
    public function getEmail(): string;
    public function getStatus(): string;
    public function getPseudo(): string;
    public function getNbForumMessage(): int;
    public function __toString();
}
