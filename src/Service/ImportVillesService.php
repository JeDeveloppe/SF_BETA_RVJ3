<?php

namespace App\Service;

use App\Entity\Ville;
use App\Repository\DepartementRepository;
use League\Csv\Reader;
use App\Repository\PartenaireRepository;
use App\Repository\PaysRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportVillesService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PartenaireRepository $partenaireRepository,
        private VilleRepository $villeRepository,
        private PaysRepository $paysRepository,
        private DepartementRepository $departementRepository
        ){
    }

    public function importVilles(SymfonyStyle $io): void
    {
        $io->title('Importation des villes');

        $villes = $this->readCsvFileVille();
        
        $io->progressStart(count($villes));

        foreach($villes as $arrayVille){
            $io->progressAdvance();
            $ville = $this->createOrUpdateVille($arrayVille);
            $this->em->persist($ville);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation des villes terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileVille(): Reader
    {
        $csvVille = Reader::createFromPath('%kernel.root.dir%/../import/ville.csv','r');
        $csvVille->setHeaderOffset(0);

        return $csvVille;
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
        ->setDepartement($this->departementRepository->findOneBy(['id' => $arrayVille['departement_id']]));

        return $ville;
    }

}