<?php

namespace App\Service\ImportRvj2;

use App\Entity\Ville;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Repository\BoiteRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DepartementRepository;
use League\Csv\ResultSet;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportVillesService
{
    public function __construct(
        private BoiteRepository $boiteRepository,
        private EntityManagerInterface $em,
        private VilleRepository $villeRepository,
        private DepartementRepository $departementRepository
        ){
    }

    public function importVilles1_5(SymfonyStyle $io): void
    {
        $io->title('Importation des villes 1/5');

        $villes = $this->readCsvFileVille(0,8000);
        
        $io->progressStart(count($villes));

        foreach($villes as $arrayVille){
            $io->progressAdvance();
            $ville = $this->createOrUpdateVille($arrayVille);
            $this->em->persist($ville);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation 1/5 terminée');
    }
    public function importVilles2_5(SymfonyStyle $io): void
    {
        $io->title('Importation des villes 2/5');

        $villes = $this->readCsvFileVille(8000,8000);
        
        $io->progressStart(count($villes));

        foreach($villes as $arrayVille){
            $io->progressAdvance();
            $ville = $this->createOrUpdateVille($arrayVille);
            $this->em->persist($ville);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation 2/5 terminée');
    }
    public function importVilles3_5(SymfonyStyle $io): void
    {
        $io->title('Importation des villes 3/5');

        $villes = $this->readCsvFileVille(16000,8000);
        
        $io->progressStart(count($villes));

        foreach($villes as $arrayVille){
            $io->progressAdvance();
            $ville = $this->createOrUpdateVille($arrayVille);
            $this->em->persist($ville);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation 3/5 terminée');
    }
    public function importVilles4_5(SymfonyStyle $io): void
    {
        $io->title('Importation des villes 4/5');

        $villes = $this->readCsvFileVille(24000,8000);
        
        $io->progressStart(count($villes));

        foreach($villes as $arrayVille){
            $io->progressAdvance();
            $ville = $this->createOrUpdateVille($arrayVille);
            $this->em->persist($ville);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation 4/5 terminée');
    }
    public function importVilles5_5(SymfonyStyle $io): void
    {
        $io->title('Importation des villes 5/5');

        $villes = $this->readCsvFileVille(32000,8000);
        
        $io->progressStart(count($villes));

        foreach($villes as $arrayVille){
            $io->progressAdvance();
            $ville = $this->createOrUpdateVille($arrayVille);
            $this->em->persist($ville);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation 5/5 terminée');
    }


    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileVille($offset, $limit): ResultSet
    {
        $csv = Reader::createFromPath('%kernel.root.dir%/../import/ville.csv','r');
        $csv->setHeaderOffset(0);
        //get 25 records starting from the 11th row
        $stmt = Statement::create()
            ->offset($offset)
            ->limit($limit)
        ;

        $records = $stmt->process($csv);
        return $records;
    }

    private function createOrUpdateVille(array $arrayVille): Ville
    {
        $ville = $this->villeRepository->findOneBy(['id' => $arrayVille['id']]);

        if(!$ville){
            $ville = new Ville();
        }

        $ville->setVilleNom($arrayVille['ville_nom'])
        ->setLat($arrayVille['lat'])
        ->setLng($arrayVille['lng'])
        ->setVilleCodePostal($arrayVille['ville_code_postal'])
        ->setVilleDepartement($arrayVille['ville_departement'])
        ->setPays($arrayVille['pays'])
        ->setDepartement($this->departementRepository->findOneBy(['id' => $arrayVille['departement_id']]))
        ->setRvj2Id($arrayVille['id']);

        return $ville;
    }

}