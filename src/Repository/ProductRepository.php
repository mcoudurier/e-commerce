<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.active = true')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAll()
    {
        return $this->createQueryBuilder('p')
            ->where('p.active = true')
            ->getQuery()
            ->getResult();
    }

    public function findAllById(array $ids)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id IN (:ids)')
            ->andWhere('p.active = true')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    public function search(?string $query, int $firstResult = 0, int $maxResults = 10)
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.name LIKE :query')
            ->andWhere('p.active = true')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->setParameter('query', '%'.addcslashes($query, '%_').'%');

        return new Paginator($query);
    }

    public function getPaginated(int $firstResult = 0, int $maxResults = 10)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.active = true')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults);

        return new Paginator($query);
    }

    public function countCurrentlySelling()
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.active = true')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
