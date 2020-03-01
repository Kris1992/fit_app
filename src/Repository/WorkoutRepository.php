<?php

namespace App\Repository;

use App\Entity\Workout;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


use Doctrine\ORM\Query\Expr\Func;

//native SQL
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @method Workout|null find($id, $lockMode = null, $lockVersion = null)
 * @method Workout|null findOneBy(array $criteria, array $orderBy = null)
 * @method Workout[]    findAll()
 * @method Workout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkoutRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Workout::class);
    }

     /**
     * findAllQuery Find all workouts or if searchTerms are not empty find all workouts with following data
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function findAllQuery(string $searchTerms)
    {   
        if ($searchTerms) {
            return $this->searchByTermsQueryLike($searchTerms);
        }
        return $this->createQueryBuilder('w')
            ->join('w.user', 'u')
            ->addSelect('u')
            ->join('w.activity', 'a')
            ->addSelect('a')
            ->getQuery()
        ;
        
    }

    /**
     * searchByTermsQuery Find all workouts with following data
     * @param  string $searchTerms Search word
     * @return Query
     */
    public function searchByTermsQueryLike(string $searchTerms)
    {
        return $this->createQueryBuilder('w')
            ->join('w.user', 'u')
            ->addSelect('u')
            ->join('w.activity', 'a')
            ->addSelect('a')
            ->andWhere('u.email LIKE :searchTerms')
            ->orWhere('a.name LIKE :searchTerms')
            ->setParameter('searchTerms', '%'.$searchTerms.'%')
            ->getQuery()
        ;
    }
    
    /**
     * @param  User $user 
     * @param  int $limit The number of workouts to return
     * @return $workouts
     */
    public function getLastWorkoutsByUser($user,int $limit)
    {
        return $this->createQueryBuilder('w')
            ->select('w')
            ->andWhere('w.user = :val')
            ->setParameter('val', $user)
            ->orderBy( 'w.startAt', 'DESC' )
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    

     /**
     * @param User $user The user object
     * @return Array[]  
     */
    public function getWorkoutsTimeAndNumWorkoutsByUser($user)
    {
        return $this->createQueryBuilder('w')
            ->select( "sum(w.durationSeconds) as totalDuration, count(w.id) as totalWorkouts")
            ->andWhere('w.user = :val')
            ->setParameter('val', $user)
            ->getQuery()
            ->getSingleResult();
    }

     
    
    /**
     * countEnergyPerDayByUserAndDateArray Gives sum of energy per day in specific time 
     * @param  User   $user     User whose is owner of workouts
     * @param  array  $timeline Array with start and end date
     * @return Array[]          
     */
    public function countEnergyPerDayByUserAndDateArray($user, array $timeline)
    {
        return $this->createQueryBuilder('w')
            ->select( "SUM(w.burnoutEnergy) AS burnoutEnergy, DATE_FORMAT(w.startAt, '%Y-%m-%d') AS startDate")
            ->groupby('startDate')
            ->andWhere('w.user = :val')
            ->andWhere('w.startAt BETWEEN :startVal AND :stopVal')
            ->setParameters(array('val' => $user, 'startVal' => $timeline['startDate'], 'stopVal' => $timeline['stopDate']))
            ->orderBy( 'startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }



//
     /**
    * @return Array[] Returns an array 
    */
    
    public function findByUserAndDateArray($user, $timeline)
    {

        return $this->createQueryBuilder('w')
            ->select( "w.id, DATE_FORMAT(w.startAt, '%Y-%m-%d') AS startDate")
            ->andWhere('w.user = :val')
            ->andWhere('w.startAt BETWEEN :startVal AND :stopVal')
            ->setParameters(array('val' => $user, 'startVal' => $timeline['startDate'], 'stopVal' => $timeline['stopDate']))
            ->orderBy( 'w.startAt', 'ASC' )
            ->getQuery()
            ->getResult();
    }


    /**
    * @return Arrray[] Returns an array
    */
    
    public function findByUserAndDateArrayNative($user, $timeline)
    {
 
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('startDate', 'startDate');

        $sql = 'SELECT `id`, DATE_FORMAT(`start_at`,  "%Y-%m-%d") AS startDate  FROM `workout` WHERE (`user_id` = :val AND `start_at` BETWEEN :startVal AND :stopVal) ORDER BY `start_at` ASC';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters(array('val' => $user, 'startVal' => $timeline['startDate'], 'stopVal' => $timeline['stopDate']));
        

        return $query->getResult();

    }

    /**
     * findAllByIds Find all workouts with given ids
     * @param  array  $arrayIds Array with at least one id
     * @return Workout[]
     */
    public function findAllByIds(array $arrayIds)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.id IN(:ids)')
            ->setParameter('ids', $arrayIds)
            ->getQuery()
            ->getResult();
    }




/*
    
    * @return Workout[] Returns an array of Workout objects
    *
    
    public function findByUserAndDate($user, $timeline)
    {

        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :val')
            ->andWhere('w.startAt BETWEEN :startVal AND :stopVal')
            ->setParameters(array('val' => $user, 'startVal' => $timeline['startDate'], 'stopVal' => $timeline['stopDate']))
            ->orderBy( 'w.startAt', 'ASC' )
            ->getQuery()
            ->getResult();
    }*/

  


    // /**
    //  * @return Workout[] Returns an array of Workout objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Workout
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
