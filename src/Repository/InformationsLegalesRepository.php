<?php

namespace App\Repository;

use App\Entity\InformationsLegales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InformationsLegales|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformationsLegales|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformationsLegales[]    findAll()
 * @method InformationsLegales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InformationsLegalesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationsLegales::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(InformationsLegales $entity, bool $flush = true): void
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
    public function remove(InformationsLegales $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return InformationsLegales[] Returns an array of InformationsLegales objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InformationsLegales
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
