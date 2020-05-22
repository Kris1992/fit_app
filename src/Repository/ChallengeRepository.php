<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Challenge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Challenge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Challenge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Challenge[]    findAll()
 * @method Challenge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChallengeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Challenge::class);
    }

    /**
     * findAllQuery Find all challenges or if searchTerms are not empty find all challenges with following data
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function findAllQuery(string $searchTerms)
    {   
        if ($searchTerms) {
            return $this->searchByTermsQuery($searchTerms);
        }
        return $this->createQueryBuilder('c')
            ->leftJoin('c.participants', 'u')
            ->addSelect('u')
            ->getQuery()
        ;
        
    }

    /**
     * searchByTermsQuery Find all challenges with following data
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function searchByTermsQuery(string $searchTerms)
    {
        return $this->createQueryBuilder('c')
            ->where('MATCH_AGAINST(c.title, c.activityName, c.activityType) AGAINST(:searchTerms boolean)>0')
            ->setParameter('searchTerms', $searchTerms.'*')
            ->getQuery()
        ;
    }

    /**
     * findAllByIds Find all challenges with given ids
     * @param  array  $arrayIds Array with at least one id
     * @return Challenge[]
     */
    public function findAllByIds(array $arrayIds)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id IN(:ids)')
            ->setParameter('ids', $arrayIds)
            ->getQuery()
            ->getResult();
    }

    /**
     * getNearestChallenges Get nearest by time challenges
     * @param  int $limit The number of challenges to return
     * @return Challenge[]
     */
    public function getNearestChallenges(int $limit)
    {
        return $this->createQueryBuilder('c')
            ->orderBy( 'c.startAt', 'ASC' )
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

}
