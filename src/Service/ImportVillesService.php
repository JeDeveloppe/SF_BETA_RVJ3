<?php

namespace App\Service;

use App\Entity\Ville;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Repository\BoiteRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DepartementRepository;
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

    public function importVilles(SymfonyStyle $io): void
    {
        $diviseur = 4;
        $csvArray = $this->calculDesRangs($diviseur);

        $io->title('Importation des villes /'.$diviseur);

        foreach($csvArray['rangs'] as $rang){
                $stmt = Statement::create()->offset($rang['start'])->limit($rang['limit']);
                $villes = $stmt->process($csvArray['csv']);

                $io->progressStart(count($villes));
                foreach($villes as $arrayVille){
                    
                    $io->progressAdvance();
                    $ville = $this->createOrUpdateVille($arrayVille);
                    $this->em->persist($ville);

                    $this->em->flush();     
                }
                $io->progressFinish();
         }

        $io->success('Importation des villes terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function calculDesRangs($diviseur)
    {
        $csvArray = [];
        $rangs = [];
        $csvVilles = Reader::createFromPath('%kernel.root.dir%/../import/ville.csv','r');
        $csvVilles->setHeaderOffset(0);

        $allRecords = count($csvVilles);

        $rang = floor($allRecords / $diviseur);

        for($i = 0; $i < $diviseur; $i++){
            $rangs[] = ['start' => $rang * $i,
            'limit' => $rang
            ];
        }

        $csvArray['rangs'] = $rangs;
        $csvArray['csv'] = $csvVilles;
        
        return $csvArray;
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