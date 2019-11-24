<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Entity\Order;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findOneByIdAndUser(int $orderId, int $userId): ?Order
    {
        return $this->createQueryBuilder('o')
            ->where('o.id = :id')
            ->andWhere('o.user = :user_id')
            ->setParameter('id', $orderId)
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
