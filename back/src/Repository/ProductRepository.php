<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    /**
     * @param int $page
     * @param int $perPage
     * @return Product[]
     */
    public function getProducts(int $page, int $perPage): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('p')
            ->from($this->getClassName(), 'p')
            ->orderBy('p.id', 'ASC')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return int
     */
    public function getCountProducts(): int
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select($queryBuilder->expr()->count('p'))
            ->from($this->getClassName(), 'p');
        return $queryBuilder->getQuery()->getFirstResult();
    }
}