<?php

namespace App\Repository;

use App\Entity\MovementSetActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MovementSetActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovementSetActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovementSetActivity[]    findAll()
 * @method MovementSetActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovementSetActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MovementSetActivity::class);
    }

    // /**
    //  * @return MovementSetActivity[] Returns an array of MovementSetActivity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MovementSetActivity
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
