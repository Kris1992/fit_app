<?php

namespace App\Repository;

use App\Entity\Reaction;
use App\Entity\Workout;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

/**
 * @method Reaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reaction[]    findAll()
 * @method Reaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reaction::class);
    }

    public static function createReactionsByTypeCriteria(int $type): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('type', $type))
        ;
    }

    public static function createReactionsByUserAndTypeCriteria(User $user, int $type): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->andX(Criteria::expr()->eq('owner', $user), Criteria::expr()->eq('type', $type)))
        ;
    }

    public function countReactionsByWorkoutAndType(Workout $workout, int $type): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select( "COUNT(r)")
            ->andWhere('r.workout = :workout AND r.type = :type')
            ->setParameters(array('workout' => $workout, 'type' => $type))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countReactionsByWorkoutsIdsGroupByType(Array $workoutsIds): Array
    {

        dump($workoutsIds);
        $result = $this->createQueryBuilder('r')
            ->select( "r.workout, r.type, COUNT(r)")
            ->andWhere('r.workout.id IN(:workoutsIds)')
            ->setParameter('workoutsIds', $workoutsIds)
            ->groupBy('r.type')
            ->getQuery()
            ->getResult()
        ;

        dump($result);
    }
    
            
    // /**
    //  * @return Reaction[] Returns an array of Reaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reaction
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
