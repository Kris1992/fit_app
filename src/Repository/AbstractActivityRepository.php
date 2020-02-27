<?php

namespace App\Repository;

use App\Entity\AbstractActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AbstractActivity|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractActivity|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractActivity[]    findAll()
 * @method AbstractActivity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbstractActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractActivity::class);
    }

    /**
    * @return Query
    */
    public function findAllQuery()
    {
        return $this->createQueryBuilder('a')
            ->getQuery()
        ;
    }

    /**
     * @return AbstractActivity[]
     */
    public function findAllNamesAlphabetical()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->execute()
        ;
    }


    // /**
    //  * @return AbstractActivity[] Returns an array of AbstractActivity objects
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
    public function findOneBySomeField($value): ?AbstractActivity
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
