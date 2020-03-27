<?php

namespace App\Repository;

use App\Entity\BodyweightActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BodyweightActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method BodyweightActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method BodyweightActivity[]    findAll()
 * @method BodyweightActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BodyweightActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BodyweightActivity::class);
    }

        /**
     * findOneActivityWithRange  Find activity by name with repetitions in range 
     * @param  string $activityName    Activity name
     * @param  int    $repetitionsAvgMin Min average repetitions
     * @param  int    $repetitionsAvgMax Max average repetitions
     * @return BodyweightActivity
     */
    public function findOneActivityWithRange(string $activityName,int $repetitionsAvgMin, int $repetitionsAvgMax)
    {
        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :activityName')
            ->andWhere('(a.repetitionsAvgMin BETWEEN :repetitionsAvgMin AND :repetitionsAvgMax) OR (a.repetitionsAvgMax BETWEEN :repetitionsAvgMin AND :repetitionsAvgMax)')
            ->setParameters([
                'activityName' => $activityName,
                'repetitionsAvgMin' => $repetitionsAvgMin,
                'repetitionsAvgMax' => $repetitionsAvgMax
            ])
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return BodyweightActivity[] Returns an array of BodyweightActivity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BodyweightActivity
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
