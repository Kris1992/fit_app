<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\MovementActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MovementActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovementActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovementActivity[]    findAll()
 * @method MovementActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovementActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MovementActivity::class);
    }

    /**
     * findOneActivityWithRange  Find activity by name with speed in range 
     * @param  string $activityName    Activity name
     * @param  float    $speedAverageMin Min average speed
     * @param  float    $speedAverageMax Max average speed
     * @return MovementActivity
     */
    public function findOneActivityWithRange(string $activityName,float $speedAverageMin, float $speedAverageMax)
    {
        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :activityName')
            ->andWhere('(a.speedAverageMin BETWEEN :speedAverageMin AND :speedAverageMax) OR (a.speedAverageMax BETWEEN :speedAverageMin AND :speedAverageMax)')
            ->setParameters([
                'activityName' => $activityName,
                'speedAverageMin' => $speedAverageMin,
                'speedAverageMax' => $speedAverageMax
            ])
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findOneActivityBySpeedAverageAndName(string $activityName, float $speedAverage)
    {
        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :activityName')
            ->andWhere('(a.speedAverageMin <= :speedAverage) AND (a.speedAverageMax >= :speedAverage)')
            ->setParameters([
                'activityName' => $activityName,
                'speedAverage' => $speedAverage,
            ])
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

}
