<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

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
}