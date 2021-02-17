<?php

namespace App\Repository;

use App\Entity\Apartments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Apartments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apartments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apartments[]    findAll()
 * @method Apartments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApartmentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apartments::class);
    }

    // /**
    //  * @return Apartments[] Returns an array of Apartments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Apartments
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
