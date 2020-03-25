<?php

namespace App\Repository;

use App\Entity\MovementSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MovementSet|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovementSet|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovementSet[]    findAll()
 * @method MovementSet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovementSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MovementSet::class);
    }

    // /**
    //  * @return MovementSet[] Returns an array of MovementSet objects
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
    public function findOneBySomeField($value): ?MovementSet
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
