<?php

namespace App\Repository;

use App\Entity\PrestationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrestationStatus>
 *
 * @method PrestationStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrestationStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrestationStatus[]    findAll()
 * @method PrestationStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrestationStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrestationStatus::class);
    }

    //    /**
    //     * @return PrestationStatus[] Returns an array of PrestationStatus objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PrestationStatus
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
