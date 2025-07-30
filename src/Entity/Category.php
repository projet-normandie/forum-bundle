<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use ProjetNormandie\ForumBundle\Controller\GetHome;
use ProjetNormandie\ForumBundle\Repository\CategoryRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name:'pnf_category')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    shortName: 'ForumCategory',
    order: ['position' => 'ASC'],
    operations: [
        new GetCollection(),
        new Get(),
        new GetCollection(
            uriTemplate: '/forum_category/get-home',
            controller: GetHome::class,
            read: false,
            normalizationContext: [
                'groups' => [
                    'category:read',
                    'category:forums',
                    'forum:read',
                    'forum:last-message',
                    'message:read',
                    'message:user',
                    'user:read:minimal',
                ]
            ],
        ),
    ],
    normalizationContext: ['groups' => ['category:read']]
)]
class Category
{
    use TimestampableEntity;

    #[Groups(['category:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private int $id;

    #[Groups(['category:read'])]
    #[ORM\Column(length: 50, nullable: false)]
    private string $name;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private int $position = 0;

    #[Groups(['category:read'])]
    #[ORM\Column(nullable: false, options: ['default' => true])]
    private bool $displayOnHome = true;

    /**
     * @var Collection<int, Forum>
     */
    #[Groups(['category:forums'])]
    #[ORM\OneToMany(targetEntity: Forum::class, mappedBy: 'category')]
    private Collection $forums;

    public function __toString()
    {
        return sprintf('%s [%s]', $this->getName(), $this->getId());
    }

    public function __construct()
    {
        $this->forums = new ArrayCollection();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setDisplayOnHome(bool $displayOnHome): void
    {
        $this->displayOnHome = $displayOnHome;
    }

    public function getDisplayOnHome(): bool
    {
        return $this->displayOnHome;
    }

    public function getForums(): Collection
    {
        return $this->forums;
    }
}
