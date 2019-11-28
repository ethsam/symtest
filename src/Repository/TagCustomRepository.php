<?php

namespace App\Repository;

use App\Entity\TagCustom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TagCustom|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagCustom|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagCustom[]    findAll()
 * @method TagCustom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagCustomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TagCustom::class);
    }

    // /**
    //  * @return TagCustom[] Returns an array of TagCustom objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TagCustom
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
