<?php

namespace App\Repository;

use App\Entity\Friend;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Friend|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friend|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friend[]    findAll()
 * @method Friend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friend::class);
    }    

    /**
     * findAllAcceptedQuery Find all accepted  friends or if searchTerms are not empty find all accepted friends with following data
     * @param  string $searchTerms Search word
     * @param  User $currentUser User object of current user
     * @param  string $status String with status
     * @return Query
     */
    public function findAllQueryByStatus(string $searchTerms, User $currentUser, string $status)
    {   
        if ($searchTerms) {
            //return $this->searchAcceptedByTermsQuery($searchTerms);
        }
        return $this->createQueryBuilder('f')
            ->andWhere('(f.inviter = :inviter OR f.invitee = :invitee) 
                AND f.status = :status')
            ->setParameters([
                'inviter' => $currentUser,
                'invitee' => $currentUser,
                'status' => $status,
            ])
            ->getQuery()
        ;
        
    }

    /**
     * searchAcceptedByTermsQuery Find all users with following data
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function searchAcceptedByTermsQuery(string $searchTerms)
    {
        return $this->createQueryBuilder('f')
            //->where('MATCH_AGAINST(u.firstName, u.secondName) AGAINST(:searchTerms boolean)>0')
            //->orWhere('u.email LIKE :emailTerms')
            //->setParameters([
            //    'searchTerms' => $searchTerms.'*',
            //    'emailTerms' => '%'.$searchTerms.'%'
            //])
            ->getQuery()
        ;
    }

    /**
     * findAllToAccept Find all friends by status "Pending" where current user is invitee
     * @param  User $currentUser User object of current user
     * @return Friend[]
     */
    public function findAllToAccept(User $currentUser)
    {   
        return $this->createQueryBuilder('f')
            ->andWhere('f.invitee = :invitee AND f.status = :status')
            ->setParameters([
                'invitee' => $currentUser,
                'status' => 'Pending',
            ])
            ->getQuery()
            ->getResult()
        ;
    }
}
