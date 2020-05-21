<?php
declare(strict_types=1);

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

    public function findOneActivityByRepetitionsPerHourAndName(string $activityName,int $repetitionsPerHour)
    {
        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :activityName')
            ->andWhere('(a.repetitionsAvgMin <= :repetitionsPerHour) AND (a.repetitionsAvgMax >= :repetitionsPerHour)')
            ->setParameters([
                'activityName' => $activityName,
                'repetitionsPerHour' => $repetitionsPerHour,
            ])
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

}
