<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity category - Сущность для записи категории продукта.
 */
#[ORM\Table]
#[ORM\Entity]
#[ORM\Index(columns: ['hash'], name: 'file_hash_idx')]
class ImportFile
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $name;
    #[ORM\Column(type: 'string', length: 32, nullable: false)]
    private string $hash;
    #[ORM\Column(name: 'upload_at', type: 'datetime', nullable: false)]
    private DateTime $uploadAt;
    #[ORM\Column(name: 'finish_at', type: 'datetime', nullable: true)]
    private ?DateTime $finishAt = null;
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $countRecord = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): ImportFile
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ImportFile
    {
        $this->name = $name;
        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): ImportFile
    {
        $this->hash = $hash;
        return $this;
    }

    public function getUploadAt(): DateTime
    {
        return $this->uploadAt;
    }

    public function setUploadAt(?DateTime $dateTime = null): ImportFile
    {
        $this->uploadAt = $dateTime ?? new DateTime();
        return $this;
    }

    public function getFinishAt(): ?DateTime
    {
        return $this->finishAt;
    }

    public function setFinishAt(?DateTime $dateTime = null): ImportFile
    {
        $this->finishAt = $dateTime ?? new DateTime();
        return $this;
    }

    public function getCountRecord(): int
    {
        return $this->countRecord;
    }

    public function setCountRecord(int $countRecord): ImportFile
    {
        $this->countRecord = $countRecord;
        return $this;
    }
}
