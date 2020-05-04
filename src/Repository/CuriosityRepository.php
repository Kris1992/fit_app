<?php

namespace App\Repository;

use App\Entity\Curiosity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
//use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Curiosity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Curiosity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Curiosity[]    findAll()
 * @method Curiosity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CuriosityRepository extends ServiceEntityRepository
{
    public function __construct(/*RegistryInterface*/ ManagerRegistry $registry)
    {
        parent::__construct($registry, Curiosity::class);
    }

    /**
     * findAllQuery Find all curiosities or if searchTerms are not empty find all curiosities with following data
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function findAllQuery(string $searchTerms)
    {   
        if ($searchTerms) {
            return $this->searchByTermsQueryLike($searchTerms);
        }
        return $this->createQueryBuilder('c')
            ->join('c.author', 'a')
            ->addSelect('a')
            ->getQuery()
        ;   
    }

    /**
     * searchByTermsQueryLike Find all curiosities with following data
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function searchByTermsQueryLike(string $searchTerms)
    {
        return $this->createQueryBuilder('c')
            ->join('c.author', 'a')
            ->addSelect('a')
            ->where('c.title LIKE :searchTerms OR c.content LIKE :searchTerms OR a.email LIKE :searchTerms')
            ->setParameter('searchTerms', '%'.$searchTerms.'%')
            ->getQuery()
        ;
    }

    /**
     * findAllByIds Find all curiosities with given ids
     * @param  array  $arrayIds Array with at least one id
     * @return Curiosity[]
     */
    public function findAllByIds(array $arrayIds)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id IN(:ids)')
            ->setParameter('ids', $arrayIds)
            ->getQuery()
            ->getResult();
    }






    // /**
    //  * @return Curiosity[] Returns an array of Curiosity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Curiosity
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
