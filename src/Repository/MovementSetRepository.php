<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\MovementSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MovementSet|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovementSet|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovementSet[]    findAll()
 * @method MovementSet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovementSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MovementSet::class);
    }

}
