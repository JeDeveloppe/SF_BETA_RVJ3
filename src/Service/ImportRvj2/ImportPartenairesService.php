<?php

namespace App\Service\ImportRvj2;

use App\Entity\Partenaire;
use League\Csv\Reader;
use App\Repository\PartenaireRepository;
use App\Repository\PaysRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportPartenairesService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PartenaireRepository $partenaireRepository,
        private VilleRepository $villeRepository,
        private PaysRepository $paysRepository
        ){
    }

    public function importPartenaires(SymfonyStyle $io): void
    {
        $io->title('Importation des partenaires');

        $partenaires = $this->readCsvFilePartenaire();
        
        $io->progressStart(count($partenaires));

        foreach($partenaires as $arrayPartenaire){
            $io->progressAdvance();
            $partenaire = $this->createOrUpdateBoite($arrayPartenaire);
            $this->em->persist($partenaire);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFilePartenaire(): Reader
    {
        $csvCatalogue = Reader::createFromPath('%kernel.root.dir%/../import/partenaires.csv','r');
        $csvCatalogue->setHeaderOffset(0);

        return $csvCatalogue;
    }

    private function createOrUpdateBoite(array $arrayPartenaire): Partenaire
    {
        $partenaire = $this->partenaireRepository->findOneBy(['id' => $arrayPartenaire['idPartenaire']]);

        if(!$partenaire){
            $partenaire = new Partenaire();
        }

        if($arrayPartenaire['complet'] == 1){
            $complet = true;
        }else{
            $complet = false;
        }
        if($arrayPartenaire['detachee'] == 1){
            $detachee = true;
        }else{
            $detachee = false;
        }
        if($arrayPartenaire['isActif'] == 1){
            $online = true;
        }else{
            $online = false;
        }


        $partenaire->setName($arrayPartenaire['nom'])
                ->setDescription($arrayPartenaire['description'])
                ->setCollecte($arrayPartenaire['collecte'])
                ->setVend($arrayPartenaire['vend'])
                ->setIsDon($arrayPartenaire['don'])
                ->setUrl($arrayPartenaire['url'])
                ->setImageBlob($arrayPartenaire['image'])
                ->setIsDetachee($detachee)
                ->setIsEcommerce($arrayPartenaire['ecommerce'])
                ->setIsComplet($complet)
                ->setIsOnLine($online)
                ->setVille($this->villeRepository->findOneBy(['rvj2Id' => $arrayPartenaire['id_villes_free']]))
                ->setCountry($this->paysRepository->findOneBy(['isoCode' => $arrayPartenaire['pays']]));

        return $partenaire;

    }
}