<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ProjetNormandie\ForumBundle\Repository\CategoryRepository;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
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
    ],
    normalizationContext: ['groups' => ['category:read']]
)]
class Category implements TimestampableInterface
{
    use TimestampableTrait;

    #[Groups(['category:read'])]
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private int $id;

    #[Groups(['category:read'])]
    #[ORM\Column(length: 50, nullable: false)]
    private string $libCategory;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private int $position = 0;

    /**
     * @var Collection<int, Forum>
     */
    #[Groups(['category:forums'])]
    #[ORM\OneToMany(targetEntity: Forum::class, mappedBy: 'category')]
    private Collection $forums;

    public function __toString()
    {
        return sprintf('%s [%s]', $this->getLibCategory(), $this->getId());
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

    public function setLibCategory(string $libCategory): void
    {
        $this->libCategory = $libCategory;
    }

    public function getLibCategory(): string
    {
        return $this->libCategory;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getForums(): Collection
    {
        return $this->forums;
    }
}
