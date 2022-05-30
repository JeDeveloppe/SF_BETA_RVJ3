<?php

namespace App\Repository;

use App\Entity\VilleBelgique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VilleBelgique|null find($id, $lockMode = null, $lockVersion = null)
 * @method VilleBelgique|null findOneBy(array $criteria, array $orderBy = null)
 * @method VilleBelgique[]    findAll()
 * @method VilleBelgique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VilleBelgiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VilleBelgique::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(VilleBelgique $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(VilleBelgique $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return VilleBelgique[] Returns an array of VilleBelgique objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VilleBelgique
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
