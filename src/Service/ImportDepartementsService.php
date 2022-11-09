<?php

namespace App\Service;

use App\Entity\Departement;
use App\Entity\Ville;
use App\Repository\DepartementRepository;
use League\Csv\Reader;
use App\Repository\PartenaireRepository;
use App\Repository\PaysRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDepartementsService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PartenaireRepository $partenaireRepository,
        private VilleRepository $villeRepository,
        private DepartementRepository $departementRepository,
        private PaysRepository $paysRepository
        ){
    }

    public function importDepartements(SymfonyStyle $io): void
    {
        $io->title('Importation des départements');

        $departements = $this->readCsvFileDepartements();
        
        $io->progressStart(count($departements));

        foreach($departements as $arrayDepartement){
            $io->progressAdvance();
            $departement = $this->createOrUpdateVille($arrayDepartement);
            $this->em->persist($departement);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation des départements terminé');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileDepartements(): Reader
    {
        $csvDepartement = Reader::createFromPath('%kernel.root.dir%/../import/departement.csv','r');
        $csvDepartement->setHeaderOffset(0);

        return $csvDepartement;
    }

    private function createOrUpdateVille(array $arrayDepartement): Departement
    {
        $departement = $this->departementRepository->findOneBy(['id' => $arrayDepartement['id']]);

        if(!$departement){
            $departement = new Departement();
        }

        $departement->setPays($this->paysRepository->findOneBy(['id' => $arrayDepartement['pays_id']]))
        ->setName($arrayDepartement['name']);

        return $departement;
    }

}