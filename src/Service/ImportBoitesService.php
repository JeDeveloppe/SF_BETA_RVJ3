<?php

namespace App\Service;

use App\Entity\Boite;
use DateTimeImmutable;
use League\Csv\Reader;
use App\Repository\BoiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportBoitesService
{
    public function __construct(
        private BoiteRepository $boiteRepository,
        private EntityManagerInterface $em
        ){
    }

    public function importBoites(SymfonyStyle $io): void
    {
        $io->title('Importation des boites');

        $boites = $this->readCsvFileCatalogue();
        
        $io->progressStart(count($boites));

        foreach($boites as $arrayBoite){
            $io->progressAdvance();
            $boite = $this->createOrUpdateBoite($arrayBoite);
            $this->em->persist($boite);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFileCatalogue(): Reader
    {
        $csvCatalogue = Reader::createFromPath('%kernel.root.dir%/../import/catalogue.csv','r');
        $csvCatalogue->setHeaderOffset(0);

        return $csvCatalogue;
    }

    private function createOrUpdateBoite(array $arrayBoite): Boite
    {
        $boite = $this->boiteRepository->findOneBy(['id' => $arrayBoite['idCatalogue']]);

        if(!$boite){
            $boite = new Boite();
        }

        $boite->setNom($arrayBoite['nom'])
            ->setEditeur($arrayBoite['editeur'])
            ->setAnnee($arrayBoite['annee'])
            ->setImageblob($arrayBoite['imageBlob'])
            ->setSlug($arrayBoite['urlNom'])
            ->setIsLivrable($arrayBoite['isLivrable'])
            ->setIsComplet($arrayBoite['isComplet'])
            ->setPoidBoite($arrayBoite['poidBoite'])
            ->setAge($arrayBoite['age'])
            ->setNbrJoueurs($arrayBoite['nbrJoueurs'])
            ->setPrixHt($arrayBoite['prix_HT'])
            ->setCreator($arrayBoite['createur'])
            ->setIsDeee($arrayBoite['deee'])
            ->setCreatedAt(new DateTimeImmutable($arrayBoite['created_at']))
            ->setIsOnLine($arrayBoite['actif']);

        return $boite;
    }

}