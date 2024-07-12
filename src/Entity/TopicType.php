<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use ProjetNormandie\ForumBundle\Repository\TopicTypeRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name:'pnf_topic_type')]
#[ORM\Entity(repositoryClass: TopicTypeRepository::class)]
#[ApiResource(
    shortName: 'ForumTopicType',
    operations: [
        new GetCollection(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['topic-type:read']]
)]
class TopicType
{
    #[Groups(['topic-type:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private int $id;

    #[Groups(['topic-type:read'])]
    #[Assert\Length(max: 30)]
    #[ORM\Column(length: 30, nullable: false)]
    private string $libType;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $position = 0;

    public function __toString()
    {
        return sprintf('%s [%s]', $this->getLibType(), $this->getId());
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setLibType(string $libType): void
    {
        $this->libType = $libType;
    }

    public function getLibType(): string
    {
        return $this->libType;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
