<?php

declare(strict_types=1);

namespace UnitTests\Repository;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class ProductRepositoryTest extends TestCase
{
    public function testGetCountProducts()
    {
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository = new ProductRepository($entityManager, new ClassMetadata(Product::class));

        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
            ->setConstructorArgs([$entityManager])
            ->getMock();

        $entityManager->expects($this->once())->method('createQueryBuilder')->willReturn($queryBuilder);

        $expr = $this->getMockBuilder(Expr::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query = $this->getMockBuilder(AbstractQuery::class)->disableOriginalConstructor()->getMock();

        $queryBuilder->expects($this->once())->method('select')->willReturn($queryBuilder);
        $queryBuilder->expects($this->once())->method('expr')->willReturn($expr);
        $expr->expects($this->once())->method('count')->with('p');
        $queryBuilder->expects($this->once())->method('from');
        $queryBuilder->expects($this->once())->method('getQuery')->willReturn($query);

        $repository->getCountProducts();
    }
}
