<?php

namespace App\Repository;

use App\Entity\Friend;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

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
     * createFriendsByInviteeCriteria Returns Friend object where invitee is given user and status is not rejected
     * @param  User   $user User object whose is or not invited by current one
     * @return Criteria
     */
    public static function createNotRejectedFriendsByInviteeCriteria(User $user): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->andX(Criteria::expr()->eq('invitee', $user), Criteria::expr()->neq('status', 'Rejected')))
        ;
    }

    /**
     * createFriendsByInviterCriteria Returns Friend object where inviter is given user  and status is not rejected
     * @param  User   $user User object whose invite or not current one
     * @return Criteria
     */
    public static function createNotRejectedFriendsByInviterCriteria(User $user): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->andX(Criteria::expr()->eq('inviter', $user), Criteria::expr()->neq('status', 'Rejected')))
        ;
    }

    /**
     * findAllQueryByStatus Find all friends with given status or if searchTerms are not empty find all friends with following data and given status
     * @param  string $searchTerms Search word
     * @param  User $currentUser User object of current user
     * @param  string $status String with status
     * @return Query
     */
    public function findAllQueryByStatus(string $searchTerms, User $currentUser, string $status)
    {   
        if ($searchTerms) {
            return $this->searchByTermsAndStatusQuery($searchTerms, $currentUser, $status);
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
     * searchByTermsAndStatusQuery Find all users with following data
     * @param  string $searchTerms Search word
     * @param  User $currentUser User object of current user
     * @param  string $status String with status
     * @return Query
     */
    public function searchByTermsAndStatusQuery(string $searchTerms, User $currentUser, string $status)
    {
        return $this->createQueryBuilder('f')
            ->join('f.inviter', 'u')
            ->addSelect('u')
            ->join('f.invitee', 'u2')
            ->addSelect('u2')
            ->andWhere('
                ((f.inviter = :currentUser OR f.invitee = :currentUser) 
                AND f.status = :status) AND ((u.email LIKE :searchTerms) OR (u.firstName LIKE :searchTerms) OR (u.secondName LIKE :searchTerms) OR (u2.email LIKE :searchTerms) OR (u2.firstName LIKE :searchTerms) OR (u2.secondName LIKE :searchTerms))')
            ->setParameters([
                'currentUser' => $currentUser,
                'status' => $status,
                'searchTerms' => '%'.$searchTerms.'%',
            ])
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

    /**
     * findAllBetweenUsers Find all types (accepted, rejected, pending...) of friends between 2 users
     * @param  User   $currentUser User object of current user
     * @param  User   $user        User object of second one
     * @return Friend|null
     */
    public function findAllBetweenUsers(User $currentUser, User $user): ?Friend
    {
        return $this->createQueryBuilder('f')
            ->andWhere('(f.invitee = :currentUser AND f.inviter = :user) OR (f.invitee = :user AND f.inviter = :currentUser)')
            ->setParameters([
                'currentUser' => $currentUser,
                'user' => $user,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * countInvitationsByUser Count all friends invitations where user is invitee
     * @param  User   $user   User object whose is invitee
     * @return int
     */
    public function countInvitationsByUser(User $user, $status = 'Pending'): int
    {
        return $this->createQueryBuilder('f')
            ->select('count(f.id)')
            ->andWhere('f.invitee = :user AND f.status = :status')
            ->setParameters([
                'user' => $user,
                'status' => $status,
            ])
            ->getQuery()
            ->getSingleScalarResult()
            ; 
    }








    //Not used for now (I found more efficient way)
    /**
     * findAllBetweenUserAndUsers Find all friends relationships between user and array of users 
     * @param  User $currentUser User object of current user
     * @param  Array $users Array with users
     * @return Friend[]
     */
    public function findAllBetweenUserAndUsers(User $currentUser, Array $users)
    {   
        return $this->createQueryBuilder('f')
            ->andWhere(
                '(f.invitee = :currentUser AND f.inviter IN(:users)) 
                OR (f.inviter = :currentUser AND f.invitee IN(:users))' )
            ->setParameters([
                'currentUser' => $currentUser,
                'users' => $users,
            ])
            ->getQuery()
            ->getResult()
        ;
    }
    
}
