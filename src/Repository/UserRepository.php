<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    /**
     * findAllQuery Find all Users or if searchTerms are not empty find all users with following data
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function findAllQuery(string $searchTerms)
    {   
        if ($searchTerms) {
            return $this->searchByTermsQuery($searchTerms);
        }
        return $this->createQueryBuilder('u')
            ->getQuery()
        ;
        
    }

    /**
     * searchByTermsQuery Find all users with following data
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function searchByTermsQuery(string $searchTerms)
    {
        return $this->createQueryBuilder('u')
            ->where('MATCH_AGAINST(u.firstName, u.secondName) AGAINST(:searchTerms boolean)>0')
            ->orWhere('u.email LIKE :emailTerms')
            ->setParameters([
                'searchTerms' => $searchTerms.'*',
                'emailTerms' => '%'.$searchTerms.'%'
            ])
            ->getQuery()
        ;
    }

    /**
     * findAllQueryElastica Find all users with following data (with join friends) or empty table 
     * @param  string $searchTerms Search word
     * @param  User $user Current user object to ignore
     * @return Query
     */
    public function findAllQueryElastica(string $searchTerms, User $user)
    {   
        if ($searchTerms) {
            return $this->createQueryBuilder('u')
                ->where('((MATCH_AGAINST(u.firstName, u.secondName) AGAINST(:searchTerms boolean)>0) OR (u.email LIKE :emailTerms)) AND u != :user')
                ->setParameters([
                    'searchTerms' => $searchTerms.'*',
                    'emailTerms' => '%'.$searchTerms.'%',
                    'user' => $user,
                ])
                ->leftJoin('u.invitedFriends', 'if')
                ->addSelect('if')
                ->leftJoin('u.invitedByFriends', 'ibf')
                ->addSelect('ibf')
                ->getQuery()
                ;
        }
        return [];
    }    

    /**
     * 
     * @return User[]
     */
    public function findAllMatching(string $query, int $limit = 5)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * findAllByIds Find all users with given ids
     * @param  array  $arrayIds Array with at least one id
     * @return User[]
     */
    public function findAllByIds(array $arrayIds)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id IN(:ids)')
            ->setParameter('ids', $arrayIds)
            ->getQuery()
            ->getResult();
    }

 
}
