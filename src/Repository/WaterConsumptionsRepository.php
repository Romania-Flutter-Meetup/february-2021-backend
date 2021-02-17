<?php

namespace App\Repository;

use App\Entity\WaterConsumptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WaterConsumptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method WaterConsumptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method WaterConsumptions[]    findAll()
 * @method WaterConsumptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WaterConsumptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WaterConsumptions::class);
    }

    // /**
    //  * @return WaterConsumptions[] Returns an array of WaterConsumptions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WaterConsumptions
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
