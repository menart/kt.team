<?php

namespace UnitTests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ProductRepositoryTest extends TestCase
{
    /**
     * @var UserService
     */
    protected $service;
    protected EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->getEntityManager();
        $this->service = new UserService($this->em, new SenderService(), new CodeGenerator());
    }

    public function testGetCountProducts()
    {
    }
}
