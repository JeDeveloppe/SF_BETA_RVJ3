<?php

namespace App\Repository;

use App\Entity\Document;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Document $entity, bool $flush = true): void
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
    public function remove(Document $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findLastEntryFromThisYear($column, $year)
    {
        return $this->createQueryBuilder('d')
            ->where('YEAR(d.createdAt) = :year')
            ->andWhere('d.'.$column.' IS NOT NULL')
            ->setParameter('year', $year)
            ->orderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAMoreRecentDevis($numberDevis)
    {
        return $this->createQueryBuilder('d')
            ->where('d.numeroDevis > :numero')
            ->setParameter('numero', $numberDevis)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOnlyDevis($number){

        return $this->createQueryBuilder('d')
            ->where('d.numeroDevis LIKE :numero')
            ->setParameter('numero','%'.$number.'%')
            ->andWhere('d.numeroFacture IS NULL')
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOnlyFactures($number){

        return $this->createQueryBuilder('d')
            ->where('d.numeroFacture LIKE :numero')
            ->setParameter('numero','%'.$number.'%')
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFacturesFromUser($user)
    {
        return $this->createQueryBuilder('d')
            ->where('d.user = :user')
            ->setParameter('user', $user)
            ->andWhere('d.numeroFacture IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findDevisFromUser($user)
    {
        return $this->createQueryBuilder('d')
            ->where('d.user = :user')
            ->setParameter('user', $user)
            ->andWhere('d.numeroFacture IS NULL')
            ->andWhere('d.endValidationDevis > :now')
            ->setParameter('now', new DateTimeImmutable('now'))
            ->andWhere('d.isDeleteByUser = :false')
            ->setParameter('false', false)
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    //[ADMIN] cherche les devis qui ont plus de X jours pour les relancer
    public function findDevisEndDelay($now){
            return $this->createQueryBuilder('d')
            ->where('d.endValidationDevis < :now')
            ->setParameter('now', $now)
            ->andWhere('d.numeroFacture IS NULL')
            ->andWhere('d.isDeleteByUser IS NULL')
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;  
    }

    
    //[ADMIN] cherche les devis supprimer par l'utilisateur et non facturer du coup
    public function findDevisDeleteByUser(){
            return $this->createQueryBuilder('d')
            ->where('d.isDeleteByUser = :delete')
            ->setParameter('delete', true)
            ->andWhere('d.numeroFacture IS NULL')
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;  
    }

    public function findActiveDevis($token){
        return $this->createQueryBuilder('d')
        ->where('d.endValidationDevis > :now ')
        ->setParameter('now', new \DateTimeImmutable('now'))
        ->andWhere('d.token = :token')
        ->setParameter('token', $token)
        ->andWhere('d.numeroFacture IS NULL')
        ->andWhere('d.isDeleteByUser = :deleted')
        ->setParameter('deleted', false)
        ->getQuery()
        ->getResult()
    ;  
}
    /*
    public function findOneBySomeField($value): ?Document
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
