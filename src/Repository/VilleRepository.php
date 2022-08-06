<?php

namespace App\Repository;


use App\Entity\Ville;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Ville|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ville|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ville[]    findAll()
 * @method Ville[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ville::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Ville $entity, bool $flush = true): void
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
    public function remove(Ville $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Ville[] Returns an array of Ville objects
    //  */

    public function findDepartmentsByPays($isoCode)
    {
        return $this->createQueryBuilder('v')
            ->groupBy('v.villeDepartement')
            ->orderBy('v.villeDepartement', 'ASC')
            ->where('v.pays = :pays')
            ->setParameter('pays', $isoCode)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findVillesFromDepartementOrderByASC($departement)
    {
        return $this->createQueryBuilder('v')
            ->groupBy('v.villeNom')
            ->orderBy('v.villeNom', 'ASC')
            ->where('v.departement = :departement')
            ->setParameter('departement', $departement)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findVillesByNamePostalCodeAndCountry(){
        return $this->createQueryBuilder('v')
            ->groupBy('v.villeNom')
            ->orderBy('v.villeNom', 'ASC')
            ;
    }
    /*
    public function findOneBySomeField($value): ?Ville
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
