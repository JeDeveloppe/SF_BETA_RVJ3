<?php

namespace App\Service;

use App\Entity\Boite;
use App\Entity\Occasion;
use DateTimeImmutable;
use League\Csv\Reader;
use App\Repository\BoiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportOccasionsService
{
    public function __construct(
        private BoiteRepository $boiteRepository,
        private EntityManagerInterface $em
        ){
    }

    public function importOccasions(SymfonyStyle $io): void
    {
        $io->title('Importation des occasions');

        $occasions = $this->readCsvFileJeuxComplets();
        
        $io->progressStart(count($occasions));

        foreach($occasions as $arrayOccasion){
            $io->progressAdvance();
            $this->createOrUpdateOccasion($arrayOccasion);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileJeuxComplets(): Reader
    {
        $csvOccasions = Reader::createFromPath('%kernel.root.dir%/../import/jeux_complets.csv','r');
        $csvOccasions->setHeaderOffset(0);

        return $csvOccasions;
    }

    private function createOrUpdateOccasion(array $arrayOccasion): void
    {
        $boite = $this->boiteRepository->findOneBy(['id' => $arrayOccasion['idCatalogue']]);

        if($boite){

            $donnees = explode("|",$arrayOccasion['vente']);

            if(count($donnees) > 1){
                $vente = true;
                $moyenAchat = $donnees[1];
                $prixVente = $donnees[0];
                $timeVente = $this->getDateTimeImmutableFromTimestamp($arrayOccasion['timeVente']);
            }else{
                $vente = false;
                $moyenAchat = null;
                $prixVente = null;
                $timeVente = null;
            }

            $occasion = new Occasion();

            $occasion
                    ->setBoite($boite)
                    ->setReference($arrayOccasion['reference'])
                    ->setPriceHt($arrayOccasion['prixHT'])
                    ->setOldPriceHt($arrayOccasion['ancienPrixHT'])
                    ->setInformation($arrayOccasion['information'])
                    ->setIsNeuf($arrayOccasion['isNeuf'])
                    ->setEtatBoite($arrayOccasion['etatBoite'])
                    ->setEtatMateriel($arrayOccasion['etatMateriel'])
                    ->setRegleJeu($arrayOccasion['regleJeu'])
                    ->setIsOnLine($arrayOccasion['actif'])
                    ->setIsDonation($arrayOccasion['don'])
                    ->setIsSale($vente)
                    ->setStock($arrayOccasion['stock'])
                    
                    ->setMeansOfSale($moyenAchat)
                    ->setPrixDeVente($prixVente)
                    ->setSale($timeVente);

            if($arrayOccasion['timeDon'] != 0){
                $occasion->setDonation($this->getDateTimeImmutableFromTimestamp($arrayOccasion['timeDon']));
            }
            $this->em->persist($occasion);
        }
    }

    private function getDateTimeImmutableFromTimestamp($timestamp)
    {
        $date = new DateTimeImmutable();

        return $date->setTimestamp($timestamp);
    }

}