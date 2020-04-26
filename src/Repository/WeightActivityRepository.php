<?php

namespace App\Repository;

use App\Entity\WeightActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method WeightActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method WeightActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method WeightActivity[]    findAll()
 * @method WeightActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeightActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WeightActivity::class);
    }

    public function findOneActivityByRepetitionsPerHourAndWeightAndName(string $activityName, int $repetitionsPerHour, float $dumbbellWeight)
    {
        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :activityName')
            ->andWhere('(a.repetitionsAvgMin <= :repetitionsPerHour) AND (a.repetitionsAvgMax >= :repetitionsPerHour) AND (a.weightAvgMin <= :dumbbellWeight) AND (a.weightAvgMax >= :dumbbellWeight)')
            ->setParameters([
                'activityName' => $activityName,
                'repetitionsPerHour' => $repetitionsPerHour,
                'dumbbellWeight' => $dumbbellWeight,
            ])
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }


    // /**
    //  * @return WeightActivity[] Returns an array of WeightActivity objects
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
    public function findOneBySomeField($value): ?WeightActivity
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
