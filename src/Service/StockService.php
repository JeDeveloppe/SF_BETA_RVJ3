<?php

namespace App\Service;

use App\Repository\ConfigurationRepository;
use App\Repository\DocumentLignesRepository;
use App\Repository\InformationsLegalesRepository;
use App\Repository\OccasionRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class StockService
{
    public function __construct(
        private DocumentLignesRepository $documentLignesRepository,
        private OccasionRepository $occasionRepository,
        private EntityManagerInterface $em
        ){
    }


    public function updateOccasionStock($document)
    {
        $lignes = $this->documentLignesRepository->findBy(['document' => $document, 'boite' => null]);
        foreach($lignes as $ligne){
            $occasion = $this->occasionRepository->findOneBy(['id' => $ligne->getOccasion()]);

            $occasion->setIsSale(true)->setIsOnLine(false)
                    ->setPrixDeVente($ligne->getPrixVente())
                    ->setDocument($document);
            $this->em->persist($occasion);
        }
        $this->em->flush();
    }

}