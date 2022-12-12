<?php

namespace App\Repository;

use App\Entity\Panier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Panier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Panier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Panier[]    findAll()
 * @method Panier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panier::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Panier $entity, bool $flush = true): void
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
    public function remove(Panier $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByUserAndNotNullColumn($columnName, $user)
    {
        return $this->createQueryBuilder('p')
            ->where('p.'.$columnName.' IS NOT NULL')
            ->andWhere('p.etat = :etat')
            ->setParameter('etat', 'panier')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findDemandesGroupeBy(){
        return $this->createQueryBuilder('p')
            ->groupBy('p.etat')
            ->where('p.etat != :etat')
            ->setParameter('etat', 'panier')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findIfOccasionIsInDemandePanier($user, $demande)
    {
        return $this->createQueryBuilder('p')
            ->where('p.boite IS NULL')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->andWhere('p.etat = :etat')
            ->setParameter('etat', $demande)
            ->getQuery()
            ->getResult()
        ;
    }
    // /**
    //  * @return Panier[] Returns an array of Panier objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Panier
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
