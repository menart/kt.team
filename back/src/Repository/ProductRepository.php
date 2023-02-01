<?php

namespace App\Repository;

use App\Dto\ProductDto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class ProductRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function getCountProducts(): int
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select($queryBuilder->expr()->count('p'))
            ->from($this->getClassName(), 'p');
        return $queryBuilder->getQuery()->getSingleScalarResult() ?? 0;
    }

    /**
     * @param ArrayCollection $batch
     * @return void
     */
    public function insertBatch(ArrayCollection $batch)
    {
        $sql = 'insert into product (category_id, "name", description, weight, created_at, updated_at) values ';

        $sql .= implode(',', $batch->map(function (ProductDto $productDto) {
            return sprintf('(%s, \'%s\', \'%s\', %s, now(), now())',
                $productDto->getCategory()->getId(),
                $productDto->getName(),
                $productDto->getDescription(),
                $productDto->getWeight()
            );
        })->toArray());
        $rsm = new ResultSetMapping;
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->execute();
        $this->getEntityManager()->clear();
    }
}