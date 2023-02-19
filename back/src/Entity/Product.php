<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity product - Сущность для записи продукта.
 */
#[ORM\Table]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Index(columns: ['name'], name: 'product_name_idx')]
#[ORM\Index(columns: ['category_id'], name: 'product_category_id_idx')]
#[ORM\HasLifecycleCallbacks]
class Product
{
    /** Уникальный идентификатор */
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 250, nullable: false)]
    private string $name;
    #[ORM\Column(type: 'string', length: 1000, nullable: false)]
    private string $description;
    #[ORM\Column(type: 'bigint', nullable: false)]
    private int $weight;
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private DateTime $updatedAt;
    #[ORM\ManyToOne(targetEntity: 'Category', cascade: ['persist', 'remove'], inversedBy: 'products')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
    private Category $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }


    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTime();
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTime();
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }
}
