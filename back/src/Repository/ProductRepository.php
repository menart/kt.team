<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Расширяем репозитория для product entity
 */
class ProductRepository extends EntityRepository
{
    public function getCountProducts(): int
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select($queryBuilder->expr()->count('p'))
            ->from($this->getClassName(), 'p');

        return $queryBuilder->getQuery()->getSingleScalarResult() ?? 0;
    }
}
