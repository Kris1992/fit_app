<?php

namespace App\Repository;

use App\Entity\Attachment;
use App\Entity\Curiosity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Attachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attachment[]    findAll()
 * @method Attachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attachment::class);
    }

    /**
     * findAllByFilenames Find all attachments with given filenames
     * @param  Array  $filenames Array with at least one filename
     * @return Attachment[] Returns an array of Attachment objects
     */
    public function findAllByFilenames(Array $filenames)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.filename IN(:filenames)')
            ->setParameter('filenames', $filenames)
            ->getQuery()
            ->getResult();
    }

    /**
     * findAllNotInFilenamesByCuriosity Find all attachments removed from curiosity
     * @param  Array     $filenames Array with at least one filename
     * @param  Curiosity $curiosity Curiosity object
     * @return Attachment[] Returns an array of Attachment objects
     */
    public function findAllNotInFilenamesByCuriosity(Array $filenames, Curiosity $curiosity)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.filename NOT IN(:filenames) AND a.curiosity = :curiosity')
            ->setParameters([
                'filenames' => $filenames,
                'curiosity' => $curiosity
            ])
            ->getQuery()
            ->getResult();
    }

}
