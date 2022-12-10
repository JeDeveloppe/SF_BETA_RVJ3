<?php

namespace App\Repository;

use App\Entity\Boite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Boite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Boite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Boite[]    findAll()
 * @method Boite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Boite::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Boite $entity, bool $flush = true): void
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
    public function remove(Boite $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findBoiteInDatabase($recherche)
    {
        return $this->createQueryBuilder('b')
            ->where('b.nom LIKE :val')
            ->setParameter('val', '%'.$recherche.'%')
            ->orderBy('b.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOccasionsInDatabase($recherche)
    {
        return $this->createQueryBuilder('b')
            ->where('b.nom LIKE :val')
            ->setParameter('val', '%'.$recherche.'%')
            ->andWhere('b.isComplet = :complet')
            ->setParameter('complet', true)
            ->orderBy('b.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOccasionsMultiCritere($recherche,$age,$nbrJoueurs)
    {
        if($nbrJoueurs == "u1" || $nbrJoueurs == "u2"){
            $addJoueurs = "b.nbrJoueurs = :joueurs";
        }else{
            $addJoueurs = "b.nbrJoueurs >= :joueurs";
        }

        return $this->createQueryBuilder('b')
            ->where('b.nom LIKE :recherche')
            ->orWhere('b.editeur LIKE :recherche')
            ->setParameter('recherche', '%'.$recherche.'%')
            ->andWhere('b.age >= :age')
            ->setParameter('age', $age)
            ->andWhere($addJoueurs)
            ->setParameter('joueurs', $nbrJoueurs)
            ->orderBy('b.nom', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    // /**
    //  * @return Boite[] Returns an array of Boite objects
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
    public function findOneBySomeField($value): ?Boite
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
