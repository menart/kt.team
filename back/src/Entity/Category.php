<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity category - Сущность для записи категории продукта.
 */
#[ORM\Table]
#[ORM\Entity]
#[ORM\Index(columns: ['name'], name: 'category_name_idx')]
#[ORM\HasLifecycleCallbacks]
class Category
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 250, nullable: false)]
    private string $name;
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private DateTime $updatedAt;
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: 'Product', cascade: ['persist', 'remove'])]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Category
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Category
    {
        $this->name = $name;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): Category
    {
        $this->createdAt = new DateTime();
        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): Category
    {
        $this->updatedAt = new DateTime();
        return $this;
    }
}
