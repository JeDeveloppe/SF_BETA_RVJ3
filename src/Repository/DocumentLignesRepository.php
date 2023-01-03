<?php

namespace App\Repository;

use App\Entity\DocumentLignes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentLignes|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentLignes|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentLignes[]    findAll()
 * @method DocumentLignes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentLignesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentLignes::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(DocumentLignes $entity, bool $flush = true): void
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
    public function remove(DocumentLignes $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findBoitesSolded(){

        return $this->createQueryBuilder('dl')
            ->join('dl.document', 'd')
            ->where('d.paiement IS NOT NULL')
            ->andWhere('dl.boite IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function findSoldedBoitesGroupeBy(){
        return $this->createQueryBuilder('dl')
         ->select('b.id as idBoite, COUNT(dl.boite) as totalBoites')
         ->innerJoin('dl.boite', 'b')
         ->where('dl.occasion IS NULL')
         ->groupBy('b.id')
         ->orderBy('COUNT(dl.boite)', 'DESC')
         ->getQuery()
         ->setMaxResults(10)
         ->getArrayResult();
     }

     public function findSoldedOccasionGroupeBy(){
        return $this->createQueryBuilder('dl')
         ->select('o.id as idOccasion, COUNT(dl.occasion) as totalBoites')
         ->innerJoin('dl.occasion', 'o')
         ->where('dl.boite IS NULL')
         ->groupBy('o.id')
         ->orderBy('COUNT(dl.occasion)', 'DESC')
         ->getQuery()
         ->getArrayResult();
     }
    // /**
    //  * @return DocumentLignes[] Returns an array of DocumentLignes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DocumentLignes
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
