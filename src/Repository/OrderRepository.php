<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findUserOrder(int $userId) : array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.userId = :val')
            ->andWhere('o.validate = 0')
            ->andWhere('o.deleteDate IS NULL')
            ->setParameter('val', $userId)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findUserOrderValidate(int $userId) : array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.userId = :val')
            ->andWhere('o.validate = 1')
            ->setParameter('val', $userId)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Order[] Returns an array of Order objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Order
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
