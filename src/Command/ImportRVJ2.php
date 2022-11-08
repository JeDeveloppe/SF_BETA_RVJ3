<?php

namespace App\Command;

use App\Service\ImportBoitesService;
use App\Service\ImportPiecesService;
use App\Service\ImportClientsService;
use App\Service\ImportAdressesService;
use App\Service\ImportOccasionsService;
use App\Service\ImportPartenairesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-RVJ2')]

class ImportRVJ2 extends Command
{
    public function __construct(
        private ImportBoitesService $importBoitesService,
        private ImportPiecesService $importPiecesService,
        private ImportOccasionsService $importOccasionsService,
        private ImportPartenairesService $importPartenairesService,
        private ImportClientsService $importClientsService,
        private ImportAdressesService $importAdressesService
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input,$output);
        
        // //on importe pour les boites
        // $this->importBoitesService->importBoites($io);

        // //on importe le detail des boites
        // $this->importPiecesService->importPieces($io);

        // //on importe les jeux complet (occasions)
        // $this->importOccasionsService->importOccasions($io);

        //on importe les jeux partenaires
        // $this->importPartenairesService->importPartenaires($io);

        //on importe les clients
        $this->importClientsService->importClients($io);

        //on importe les adresses de facturation
        $this->importAdressesService->importAdresses($io);

        return Command::SUCCESS;
    }
}