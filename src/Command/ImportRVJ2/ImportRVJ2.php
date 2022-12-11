<?php

namespace App\Command\ImportRVJ2;

use Symfony\Component\Console\Command\Command;
use App\Service\ImportRvj2\ImportBoitesService;
use App\Service\ImportRvj2\ImportPiecesService;
use App\Service\ImportRvj2\ImportVillesService;
use App\Service\ImportRvj2\ImportClientsService;
use App\Service\ImportRvj2\ImportAdressesService;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\ImportRvj2\ImportDocumentsService;
use App\Service\ImportRvj2\ImportOccasionsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use App\Service\ImportRvj2\ImportPartenairesService;
use App\Service\ImportRvj2\ImportDepartementsService;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\ImportRvj2\ImportDocumentsLignesService;
use App\Service\ImportRvj2\CreationAdministrateurAdresseService;

#[AsCommand(name: 'app:importRVJ2')]

class ImportRVJ2 extends Command
{
    public function __construct(
        private ImportBoitesService $importBoitesService,
        private ImportPiecesService $importPiecesService,
        private ImportOccasionsService $importOccasionsService,
        private ImportPartenairesService $importPartenairesService,
        private ImportClientsService $importClientsService,
        private ImportAdressesService $importAdressesService,
        private ImportDepartementsService $importDepartementsService,
        private ImportVillesService $importVillesService,
        private CreationAdministrateurAdresseService $creationAdministrateurAdresseService,
        private ImportDocumentsService $importDocumentsService,
        private ImportDocumentsLignesService $importDocumentsLignesService
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input,$output);

        // //on importe les boites
        // $this->importBoitesService->importBoites($io);

        // ////on importe le detail des boites
        // $this->importPiecesService->importPieces($io);

        // // //on importe les jeux complet (occasions)
        // $this->importOccasionsService->importOccasions($io);

        // //on importe les departementss
        // $this->importDepartementsService->importDepartements($io);

        // //on importe les villes
        // ini_set('memory_limit', '512M');
        // $this->importVillesService->importVilles1_5($io);
        // $this->importVillesService->importVilles2_5($io);
        // $this->importVillesService->importVilles3_5($io);
        // $this->importVillesService->importVilles4_5($io);
        // $this->importVillesService->importVilles5_5($io);

        // //on importe les partenaires
        // $this->importPartenairesService->importPartenaires($io);

        // //on importe les clients
        $this->importClientsService->importClients($io);



        // //on importe les adresses (facturation et livraison)
        // $this->importAdressesService->importAdresses($io);

        //on crée user administrateur et adresse de retrait
        // $this->creationAdministrateurAdresseService->creationAdminAdresse($io);

        // //on importe les documents
        // $this->importDocumentsService->importDocuments($io);

        // //on importe les ligne boite des documents
        // $this->importDocumentsLignesService->importDocumentsLigneBoites($io);

        // //on importe les lignes occasion des documents
        // $this->importDocumentsLignesService->importDocumentsLigneOccasion($io);

        return Command::SUCCESS;
    }
}