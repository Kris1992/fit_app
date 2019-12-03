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
    * @return Query
    */
    public function findAllQuery()
    {
        return $this->createQueryBuilder('w')
            ->join('w.user', 'u')
            ->addSelect('u')
            ->join('w.activity', 'a')
            ->addSelect('a')
            ->getQuery()
        ;
    }

    
    /**
     * @param  $user 
     * @param  $limit The number of workouts to return
     * @return $workouts
     */
    public function getLastWorkoutsByUser($user, $limit)
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
     * @return Array[] Returns an array  
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
    * @return Array[] Returns an array 
    */
    
    public function countEnergyPerDayByUserAndDateArray($user, $timeline)//wtf?
    {

        return $this->createQueryBuilder('w')
            ->select( "w.burnoutEnergy, DATE_FORMAT(w.startAt, '%Y-%m-%d') AS startDate")
            ->andWhere('w.user = :val')
            ->andWhere('w.startAt BETWEEN :startVal AND :stopVal')
            ->setParameters(array('val' => $user, 'startVal' => $timeline['startDate'], 'stopVal' => $timeline['stopDate']))
            ->orderBy( 'w.startAt', 'ASC' )
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
