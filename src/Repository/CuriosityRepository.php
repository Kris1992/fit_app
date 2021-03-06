<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Curiosity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Curiosity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Curiosity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Curiosity[]    findAll()
 * @method Curiosity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CuriosityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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

    /**
     * findPublishedOrderedByNewest Find published curiosities (order by newest)
     * @param int $limit The max number curiosities to return
     * @return Curiosity[]
     */
    public function findPublishedOrderedByNewest(int $limit)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.publishedAt IS NOT NULL')
            ->orderBy('c.id', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

}
