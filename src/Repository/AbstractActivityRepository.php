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
     * findAllQuery Find all activities or if searchTerms are not empty find all activities with following data
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function findAllQuery(string $searchTerms)
    {   
        if ($searchTerms) {
            return $this->searchByTermsQuery($searchTerms);
            //return $this->searchByTermsQueryLike($searchTerms);
        }
        return $this->createQueryBuilder('a')
            ->getQuery()
        ;
        
    }

    /**
     * searchByTermsQuery Find all activities with following data
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function searchByTermsQuery(string $searchTerms)
    {
        return $this->createQueryBuilder('a')
            ->where('MATCH_AGAINST(a.type, a.name) AGAINST(:searchTerms boolean)>0')
            ->setParameter('searchTerms', $searchTerms.'*')
            ->getQuery()
        ;
    }

    /**
     * searchByTermsQueryLike Find all activities with following data using LIKE
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function searchByTermsQueryLike(string $searchTerms)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.type LIKE :searchTerms')
            ->orWhere('a.name LIKE :searchTerms')
            ->setParameter('searchTerms', '%'.$searchTerms.'%')
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


    /**
     * findByTypeNamesAlphabetical Find all activities by given type
     * @param  string $type Type of activity
     * @return AbstractActivity[]
     */
    public function findByTypeNamesAlphabetical(string $type)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.type LIKE :type')
            ->setParameter('type', $type)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * findAllByIds Find all activities with given ids
     * @param  array  $arrayIds Array with at least one id
     * @return AbstractActivity[]
     */
    public function findAllByIds(array $arrayIds)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id IN(:ids)')
            ->setParameter('ids', $arrayIds)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return string
     */
    public function findByTypeUniqueNamesAlphabetical($type = null)
    {
        if ($type === null) {
            return $this->findUniqueNamesAlphabetical();
        } else {
            return $this->createQueryBuilder('a')
                ->select('DISTINCT a.name' )
                ->andWhere('a.type LIKE :type')
                ->setParameter('type', $type)
                ->orderBy('a.name', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }    
    }

    /**
     * @return Array[]
     */
    public function findUniqueNamesAlphabetical()
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.name' )
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return string
     */
    public function findUniqueNamesAlphabeticalByType($type)
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.name')
            ->andWhere('a.type LIKE :type')
            ->setParameter('type', $type)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult()
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
