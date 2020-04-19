<?php

namespace App\Repository;

use App\Entity\Workout;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry)
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
            ->select( "SUM(w.durationSecondsTotal) as totalDuration, count(w.id) as totalWorkouts")
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
            ->select( "SUM(w.burnoutEnergyTotal) AS burnoutEnergy, DATE_FORMAT(w.startAt, '%Y-%m-%d') AS startDate")
            ->andWhere('w.user = :user')
            //->andWhere('w.startAt BETWEEN :startVal AND :stopVal')
            ->andWhere('w.startAt >= :startVal AND w.startAt <= :stopVal')
            ->setParameters(array('user' => $user, 'startVal' => $timeline['startDate'], 'stopVal' => $timeline['stopDate']))
            ->groupby('startDate')
            ->orderBy( 'startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }


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

    /**
     * getHighScoresByUser
     * @param  User $user User whose registry workouts
     * @return Arrray[] Returns an array
     */
    public function getHighScoresByUser($user)
    {
        return $this->createQueryBuilder('w')
            ->innerJoin('w.activity', 'a')
            ->select('a.name AS activityName, MAX(w.durationSecondsTotal) AS totalDuration, MAX(w.distanceTotal) AS totalDistance, MAX(w.burnoutEnergyTotal) AS totalBurnoutEnergy')
            ->andWhere('w.user = :user')
            ->setParameter('user', $user)
            ->groupBy('activityName')
            ->getQuery()
            ->getResult();
    }

    public function getHighScoresByUserNative($user)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('activity_name', 'activityName');
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('totalDuration', 'totalDuration');
        $rsm->addScalarResult('totalDistance', 'totalDistance');
        $rsm->addScalarResult('totalBurnoutEnergy', 'totalBurnoutEnergy');

        $sql = 'SELECT MAX(w.duration_seconds_total) AS totalDuration, MAX(w.distance_total) AS totalDistance, MAX(w.burnout_energy_total) AS totalBurnoutEnergy, a.name AS activity_name FROM workout w INNER JOIN abstract_activity a ON w.activity_id = a.id WHERE (w.user_id = :user) GROUP BY activity_name';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters(array('user' => $user));

        return $query->getResult();
    }


    public function getHighScoresByUserTest($user)
    {

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('activity_name', 'activityName');
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('totalDuration', 'totalDuration');
        $rsm->addScalarResult('totalDistance', 'totalDistance');
        $rsm->addScalarResult('totalBurnoutEnergy', 'totalBurnoutEnergy');

    
        $sql = 'SELECT w.id, w.duration_seconds_total AS totalDuration, a.name AS activity_name FROM workout w INNER JOIN abstract_activity a ON w.activity_id = a.id WHERE (w.user_id = :user) AND (a.name, w.duration_seconds_total) IN (SELECT ah.name, MAX(wh.duration_seconds_total) FROM workout wh INNER JOIN abstract_activity ah ON wh.activity_id = ah.id WHERE (wh.user_id = :user) GROUP BY ah.name)';

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameters(array('user' => $user));

        // value IN (SELECT column-name
        //           FROM table-name2 
         //         WHERE condition)
        //$sql = "SELECT u.id, u.name, a.id AS address_id, a.street, a.city " . 
       //"FROM users u INNER JOIN address a ON u.address_id = a.id";
       //INNER JOIN activity a ON `w.activity_id` = `a.id`

        dd($query->getResult());
        /*
SELECT * 
FROM t1 WHERE (id,rev) IN 
( SELECT id, MAX(rev)
  FROM t1
  GROUP BY id
)
        $qb2 = $this->createQueryBuilder('wHigh')
                ->innerJoin('wHigh.activity', 'ac')
                ->select('ac.name AS activityName, MAX(wHigh.durationSecondsTotal) AS totalDuration, MAX(wHigh.distanceTotal) AS totalDistance, MAX(wHigh.burnoutEnergyTotal) AS totalBurnoutEnergy')
                ->andWhere('wHigh.user = :user')
                ->setParameter('user', $user)
                ->groupBy('activityName')
                ;

        $qb = $this->createQueryBuilder('w')
                ->innerJoin('w.activity', 'a')
                ->select('w')
                ->where('w.user = :user')
                ->setParameter('user', $user)
                ;


        $qb
            ->andWhere(
                $qb->expr()->eq( 'w.durationSecondsTotal','('..')')
            )
            ->getQuery()
            ->getResult();
        */
        

                
                //->getQuery()
                //->getResult();



    /*select all
    a potem where 
    select total 
    name i total = name i total
    gruop by*/

    
    /*$qb2= $this->createQueryBuilder('w')
            ->select('MAX(w.durationSecondsTotal) AS totalDuration, MAX(w.distanceTotal) AS totalDistance, MAX(w.burnoutEnergyTotal) AS totalBurnoutEnergy')
            ->andWhere('w.user = :user')
            ->setParameter('user', $user)
            ;

    ->innerJoin('', 'm', 'WITH', $qb->expr()->eq( 'm.periodeComptable', '('.$qb2->getDQL().')' ))
        ->where('a = :affaire')
        ->setParameter('affaire', $affaire)
        ;*/

    return $qb->getQuery()->getResult();


        /*return $this->createQueryBuilder('w')
            ->innerJoin('w.activity', 'a')
            ->select('a.name AS activityName, MAX(w.durationSecondsTotal) AS totalDuration, MAX(w.distanceTotal) AS totalDistance, MAX(w.burnoutEnergyTotal) AS totalBurnoutEnergy')
            ->andWhere('w.user = :user')
            ->setParameter('user', $user)
            ->groupBy('activityName')
            ->getQuery()
            ->getResult();*/
    }



    /**
     * findByUserBeforeDate Find workouts handled by user with startAt date before given 
     * @param  User $user  User whose handling workouts
     * @param  string $date  Date before which we will search workouts
     * @param  int    $limit Number of workouts to return
     * @return Workout[]
     */
    public function findByUserBeforeDate($user,string $date, int $limit)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user AND w.startAt < :date')
            ->setParameters([
                'user' => $user,
                'date' => $date
            ])
            ->orderBy( 'w.startAt', 'DESC' )
            ->setMaxResults($limit)
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
